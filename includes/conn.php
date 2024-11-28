<?php
$conn = oci_connect('YZLM_OLD', 'BYZYZ', 'BYZDB', "AL32UTF8");
if (!$conn) {
    die("Veritabanına bağlanılamadı.");
}
?>