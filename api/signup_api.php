<?php
$conn = oci_connect('YZLM_OLD', 'BYZYZ', 'BYZDB', "AL32UTF8");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ad = htmlspecialchars($_POST['ad']);
    $soyad = htmlspecialchars($_POST['soyad']);
    $email = htmlspecialchars($_POST['email']);
    $sifre = htmlspecialchars($_POST['sifre']);

    $query = "INSERT INTO MTH_USERS (NAME, SURNAME, EMAIL, PASSWORD) 
              VALUES (:ad, :soyad, :email, :sifre)";
    
    $stmt = oci_parse($conn, $query);
    
    oci_bind_by_name($stmt, ':ad', $ad);
    oci_bind_by_name($stmt, ':soyad', $soyad);
    oci_bind_by_name($stmt, ':email', $email);
    oci_bind_by_name($stmt, ':sifre', $sifre);
    
    $result = oci_execute($stmt);

    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Kullanıcı başarıyla kaydedildi.']);
        header("Location: /index.php?status=success");
        exit();
    } else {
        $e = oci_error($stmt);
        echo json_encode(['success' => false, 'message' => 'Kayıt başarısız: ' . $e['message']]);
    }
    oci_free_statement($stmt);
    oci_close($conn);
}
?>