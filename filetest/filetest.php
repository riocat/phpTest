<?php
$file = fopen("20190422215214220_2G.txt", "r") or exit("无法打开文件!");
// 读取文件每一行，直到文件结尾
while (!feof($file)) {
    echo fgets($file) . "<br>";
}
fclose($file);