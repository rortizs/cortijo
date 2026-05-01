<?php

require_once("../../models/dynamic.php");
require_once("../../models/SimpleImage.php");
$dynamic = new Dynamic();
$response = "";
$file = $_FILES['file']['name'];
$full_local_path = '/var/www/kairosV2/assets/images/empleados/' . $file;
$upload = move_uploaded_file($_FILES['file']['tmp_name'], $full_local_path);
if ($upload == true) {
    $updateImg = $dynamic->updateImage($_REQUEST['id'], 'assets/images/empleados/' . $file);
    $response[] = array('message' => 'uploadSuccess');
    //resize
    try {
        $img = new abeautifulsite\SimpleImage('../../assets/images/empleados/' . $file . '');
        $img->best_fit(500, 500)->save('../../assets/images/empleados/' . $file . '');
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
} else {
    $response[] = array('message' => 'uploadFailed');
}
echo json_encode($response);

