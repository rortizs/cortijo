<?php

$controller = file_get_contents(__DIR__ . '/../erp/controllers/inventariosController.php');

function extractBetween($source, $startNeedle, $endNeedle)
{
    $start = strpos($source, $startNeedle);
    if ($start === false) {
        throw new Exception('Missing start needle: ' . $startNeedle);
    }
    $end = strpos($source, $endNeedle, $start);
    if ($end === false) {
        throw new Exception('Missing end needle: ' . $endNeedle);
    }
    return substr($source, $start, $end - $start);
}

function assertSameValue($expected, $actual, $message)
{
    if ($expected !== $actual) {
        throw new Exception($message . ' Expected ' . var_export($expected, true) . ' got ' . var_export($actual, true));
    }
}

function assertContains($haystack, $needle, $message)
{
    if (strpos($haystack, $needle) === false) {
        throw new Exception($message . "\nOutput: " . $haystack);
    }
}

$authGuardOriginal = extractBetween(
    $controller,
    "if (empty(\$_SESSION['userName']) || empty(\$_SESSION['idRoles']))",
    '$dbC = Config::$dbD;'
);
$countryGuardOriginal = extractBetween(
    $controller,
    "if (isset(\$_POST['table']) && \$_POST['table'] == 'empresas')",
    '$tableStructure ='
);

assertContains($authGuardOriginal, 'exit;', 'Unauthenticated guard must terminate before database/service setup');
if (substr_count($countryGuardOriginal, 'exit;') < 2) {
    throw new Exception('Country guard must terminate before the dynamic save path for malformed and non-existent country values');
}

$authGuard = str_replace('exit;', 'return;', $authGuardOriginal);
$countryGuard = str_replace('exit;', 'return;', $countryGuardOriginal);

class AdminCountryGuardStub
{
    public $existingIds;

    public function __construct($existingIds)
    {
        $this->existingIds = $existingIds;
    }

    public function paisExiste($idPais)
    {
        return in_array($idPais, $this->existingIds, true);
    }
}

function runExtractedGuard($guardSource, $sessionData, $postData, $existingCountryIds)
{
    global $admin;
    $_SESSION = $sessionData;
    $_POST = $postData;
    $admin = new AdminCountryGuardStub($existingCountryIds);
    http_response_code(200);

    $callable = eval('return function () use ($admin) { ' . $guardSource . ' };');
    ob_start();
    $callable();
    $output = ob_get_clean();

    return array(
        'code' => http_response_code(),
        'output' => $output,
        'post' => $_POST,
    );
}

$validSession = array(
    'userName' => 'tester',
    'idRoles' => '1',
    'dbProject' => 'erp_elcortijo',
    'idEmpresa' => '1',
    'idEmpresas' => '1'
);

$basePost = array(
    'table' => 'empresas',
    'data' => array(
        array('idPaises' => '1')
    )
);

$unauthorized = runExtractedGuard($authGuard, array(), $basePost, array(1));
assertSameValue(401, $unauthorized['code'], 'Unauthenticated save must return HTTP 401');
assertContains($unauthorized['output'], 'no autorizada', 'Unauthenticated save must return an authorization error message');

$malformedPost = $basePost;
$malformedPost['data'][0]['idPaises'] = '1abc';
$malformed = runExtractedGuard($countryGuard, $validSession, $malformedPost, array(1));
assertSameValue(400, $malformed['code'], 'Malformed idPaises must return HTTP 400');
assertContains($malformed['output'], 'error', 'Malformed idPaises must return a clear country error');

$zeroPost = $basePost;
$zeroPost['data'][0]['idPaises'] = '0';
$zero = runExtractedGuard($countryGuard, $validSession, $zeroPost, array(1));
assertSameValue(400, $zero['code'], 'Zero idPaises must return HTTP 400');
assertContains($zero['output'], 'error', 'Zero idPaises must return a clear country error');

$missingPost = $basePost;
$missingPost['data'][0]['idPaises'] = '999';
$missing = runExtractedGuard($countryGuard, $validSession, $missingPost, array(1));
assertSameValue(400, $missing['code'], 'Non-existent idPaises must return HTTP 400');
assertContains($missing['output'], 'error', 'Non-existent idPaises must return a clear country error');

$validPost = $basePost;
$validPost['data'][0]['idPaises'] = '01';
$valid = runExtractedGuard($countryGuard, $validSession, $validPost, array(1));
assertSameValue(200, $valid['code'], 'Existing idPaises must pass the country guard');
assertSameValue('1', $valid['post']['data'][0]['idPaises'], 'Existing idPaises must be normalized before dynamic save');

echo "PR #17 controller guard behavior checks passed\n";
