<?php
$conn = oci_connect('KULLANICI', 'SIFRE');
if (!$conn) {
    die("Veritabanına bağlanılamadı.");
}
?>