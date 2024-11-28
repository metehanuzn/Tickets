<?php
session_start();

if (!isset($_SESSION['user_data'])) {
    echo json_encode(['status' => 'error', 'message' => 'Oturum süresi doldu.']);
    exit;
}
$userId = $_SESSION['user_data']['ID'];

$requestId = isset($_POST['request_id']) ? intval($_POST['request_id']) : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($requestId === 0 || empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Talep ID veya mesaj eksik.']);
    exit;
}

$conn = oci_connect('YZLM_OLD', 'BYZYZ', 'BYZDB', "AL32UTF8");
if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Veritabanına bağlanılamadı.']);
    exit;
}

$sqlInsert = "INSERT INTO MTH_REQUESTS_MESSAGES (MESSAGE, MSG_DATE, REQ_ID, SENDER_ID) 
              VALUES (:message, SYSDATE, :req_id, :sender_id)";  
$stmtInsert = oci_parse($conn, $sqlInsert);
oci_bind_by_name($stmtInsert, ':message', $message);
oci_bind_by_name($stmtInsert, ':req_id', $requestId);
oci_bind_by_name($stmtInsert, ':sender_id', $userId); 

if (oci_execute($stmtInsert)) {
    oci_free_statement($stmtInsert);
    oci_close($conn);
    echo json_encode(['status' => 'success', 'message' => 'Mesaj başarıyla gönderildi.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Mesaj gönderilirken hata oluştu.']);
}
