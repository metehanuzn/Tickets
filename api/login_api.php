<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

try {
    $conn = oci_connect('YZLM_OLD', 'BYZYZ', 'BYZDB', "AL32UTF8");

    if (!$conn) {
        $error = oci_error();
        echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $error['message']]);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    $email = $input['EMAIL'] ?? null;
    $password = $input['PASSWORD'] ?? null;

    if (!$email || !$password) {
        echo json_encode(['success' => false, 'message' => 'Email ve şifre gerekli.']);
        exit;
    }

    $sql = "SELECT * FROM MTH_USERS WHERE EMAIL = :email AND PASSWORD = :password";
    $stmt = oci_parse($conn, $sql);

    oci_bind_by_name($stmt, ':email', $email);
    oci_bind_by_name($stmt, ':password', $password);

    oci_execute($stmt);

    if ($row = oci_fetch_assoc($stmt)) {
        session_start();
        $_SESSION['user_data'] = $row;

        echo json_encode(['success' => true, 'message' => 'Giriş başarılı']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Geçersiz email veya şifre.']);
    }

    oci_free_statement($stmt);
    oci_close($conn);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
