<?php
/**
 * Lightweight source regression check for Cortijo dashboard KPI semantics.
 *
 * This project has no local test harness or reliable local DB fixture, so this
 * script protects the dashboard sales/purchase KPI regression by inspecting the
 * legacy model SQL sources that feed the dashboard cards.
 */

$root = dirname(__DIR__);
$adminPath = $root . '/erp/models/admin.php';
$reportesPath = $root . '/erp/models/reportes.php';

$failures = [];

function failIfFalse($condition, $message)
{
    global $failures;
    if (!$condition) {
        $failures[] = $message;
    }
}

function readSource($path)
{
    if (!is_file($path)) {
        fwrite(STDERR, "Missing source file: {$path}\n");
        exit(2);
    }

    return file_get_contents($path);
}

function methodBody($source, $methodName)
{
    $needle = 'public function ' . $methodName;
    $start = strpos($source, $needle);
    if ($start === false) {
        return '';
    }

    $next = strpos($source, 'public function ', $start + strlen($needle));
    if ($next === false) {
        return substr($source, $start);
    }

    return substr($source, $start, $next - $start);
}

function unionSelect($source, $document)
{
    $pattern = "/(?:select|SELECT)\\s+'" . preg_quote($document, '/') . "'\\s+as\\s+documento.*?(?=\\n\\s*union all|;\")/is";
    if (preg_match($pattern, $source, $matches)) {
        return $matches[0];
    }

    return '';
}

function hasSqlFragment($sql, $fragment)
{
    $normalizedSql = preg_replace('/\s+/', ' ', strtolower($sql));
    $normalizedFragment = preg_replace('/\s+/', ' ', strtolower($fragment));

    return strpos($normalizedSql, $normalizedFragment) !== false;
}

function assertSqlHas($sql, $fragment, $message)
{
    failIfFalse($sql !== '' && hasSqlFragment($sql, $fragment), $message);
}

$admin = methodBody(readSource($adminPath), 'resumenDocumentosOperados');
$ventasHoy = methodBody(readSource($reportesPath), 'ventasHoy');
$comprasHoy = methodBody(readSource($reportesPath), 'comprasHoy');

failIfFalse($admin !== '', 'Admin::resumenDocumentosOperados() was not found.');
failIfFalse($ventasHoy !== '', 'Reportes::ventasHoy() was not found.');
failIfFalse($comprasHoy !== '', 'Reportes::comprasHoy() was not found.');

foreach (['cotizaciones', 'totalCotizado', 'compras', 'totalCompras'] as $document) {
    failIfFalse(unionSelect($admin, $document) !== '', "Dashboard KPI document key '{$document}' must remain present for JS compatibility.");
}

$cotizaciones = unionSelect($admin, 'cotizaciones');
assertSqlHas($cotizaciones, 'from ventas', 'cotizaciones KPI must read ventas.');
assertSqlHas($cotizaciones, 'DATE(fechaFactura)=CURDATE()', 'cotizaciones KPI must use fechaFactura as the business date.');
assertSqlHas($cotizaciones, 'tipoTransaccion = 1', 'cotizaciones KPI must count sale transactions only.');
assertSqlHas($cotizaciones, 'autorizacionFEL IS NOT NULL', 'cotizaciones KPI must count FEL-authorized sales only.');
assertSqlHas($cotizaciones, 'anulacion = 0', 'cotizaciones KPI must exclude annulled sales.');

$totalCotizado = unionSelect($admin, 'totalCotizado');
assertSqlHas($totalCotizado, 'IFNULL(SUM(total), 0)', 'totalCotizado KPI must return numeric zero when there are no sales.');
assertSqlHas($totalCotizado, 'DATE(fechaFactura)=CURDATE()', 'totalCotizado KPI must use fechaFactura as the business date.');
assertSqlHas($totalCotizado, 'tipoTransaccion = 1', 'totalCotizado KPI must sum sale transactions only.');
assertSqlHas($totalCotizado, 'autorizacionFEL IS NOT NULL', 'totalCotizado KPI must sum FEL-authorized sales only.');
assertSqlHas($totalCotizado, 'anulacion = 0', 'totalCotizado KPI must exclude annulled sales.');

$compras = unionSelect($admin, 'compras');
assertSqlHas($compras, 'from compras', 'compras KPI must read compras.');
assertSqlHas($compras, 'DATE(fechaFactura)=CURDATE()', 'compras KPI must use fechaFactura as the purchase business date.');

$totalCompras = unionSelect($admin, 'totalCompras');
assertSqlHas($totalCompras, 'IFNULL(SUM(total), 0)', 'totalCompras KPI must return numeric zero when there are no purchases.');
assertSqlHas($totalCompras, 'DATE(fechaFactura)=CURDATE()', 'totalCompras KPI must use fechaFactura as the purchase business date.');

failIfFalse(strpos($ventasHoy, 'echo $sql') === false, 'Reportes::ventasHoy() must not echo SQL before JSON output.');
foreach ([$ventasHoy => 'Reportes::ventasHoy()', $comprasHoy => 'Reportes::comprasHoy()'] as $body => $label) {
    assertSqlHas($body, "fechaFactura >= DATE_FORMAT(CURDATE(), '%Y-%m-01')", "{$label} MesHoy must use a half-open current-month start.");
    assertSqlHas($body, 'fechaFactura < DATE_ADD(CURDATE(), INTERVAL 1 DAY)', "{$label} MesHoy must include today's rows after midnight.");
    assertSqlHas($body, "fechaFactura >= DATE_SUB(DATE_FORMAT(CURDATE(), '%Y-%m-01'), INTERVAL 1 MONTH)", "{$label} MesAyer must use a half-open previous-month start.");
    assertSqlHas($body, 'fechaFactura < DATE_SUB(DATE_ADD(CURDATE(), INTERVAL 1 DAY), INTERVAL 1 MONTH)', "{$label} MesAyer must use a half-open previous-month end.");
}
assertSqlHas($comprasHoy, 'DATE(fechaFactura) = CURDATE()', 'Reportes::comprasHoy() daily filter must handle DATETIME fechaFactura values.');

if ($failures !== []) {
    fwrite(STDERR, "Dashboard KPI regression check failed:\n");
    foreach ($failures as $failure) {
        fwrite(STDERR, "- {$failure}\n");
    }
    exit(1);
}

echo "Dashboard KPI regression check passed.\n";
