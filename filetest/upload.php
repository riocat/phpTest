<?php
/*if (!move_uploaded_file($_FILES['upload_file']['tmp_name'], "../fileTemp/" . $_FILES['upload_file']['name'])) {
    echo "error";
} else {
    echo "success";
}*/


require_once "excelUtil.php";

$execl = new excelUtil();

if( $_FILES['upload_file']['name']) {

}else{

}

$fs = "../fileTemp/" ."100.xlsx";

echo $fs;

$execl->data_import($fs, substr($fs, strrpos($fs, '.')+1));