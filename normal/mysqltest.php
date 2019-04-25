<?php
$con = mysqli_connect("localhost", "root", "abcd1234");
if (!$con) {
    die('Could not connect: ' . mysql_error());
} else {
    echo "success";
}

mysqli_select_db($con,"lottery");

$sqlstr = "INSERT INTO `importlot` (`id`, `no`, `ball1`, `ball2`, `ball3`, `ball4`, `ball5`, `ball6`, `ball7`, `result_string`) VALUES ('12', '7012', '3', '12', '15', '29', '34', '7', '11', '03121529340711');";

//$sqlstr = "SELECT * FROM importlot";

if (mysqli_query($con, $sqlstr)) {
    echo "插入成功";
} else {
    echo "插入失败";
}

mysqli_close($con);