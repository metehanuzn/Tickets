<?php
session_start();
if (!isset($_SESSION['user_data'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_data']['ID'];

$requestId = isset($_GET['request_id']) ? intval($_GET['request_id']) : 0;

if ($requestId === 0) {
    echo "Talep ID eksik veya geçersiz.";
    exit;
}

include(includes/conn)
if (!$conn) {
    die("Veritabanına bağlanılamadı.");
}

$sql = "SELECT * FROM MTH_REQUESTS WHERE ID = :request_id AND U_ID = :user_id";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':request_id', $requestId);
oci_bind_by_name($stmt, ':user_id', $userId);

oci_execute($stmt);

if ($row = oci_fetch_assoc($stmt)) {
    $sqlMessages = "
        SELECT m.MESSAGE, m.MSG_DATE, u.NAME, u.SURNAME 
        
        FROM MTH_REQUESTS_MESSAGES m

        JOIN MTH_USERS u ON m.SENDER_ID = u.ID

        WHERE m.REQ_ID = :req_id

        ORDER BY m.MSG_DATE";

    $stmtMessages = oci_parse($conn, $sqlMessages);
    oci_bind_by_name($stmtMessages, ':req_id', $requestId);
    oci_execute($stmtMessages);

    $messages = [];
    while ($row = oci_fetch_assoc($stmtMessages)) {
        $messages[] = $row;
    }

    oci_free_statement($stmtMessages);
    oci_close($conn);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Mesaj</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .container-fluid {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        #messagePanel {
            max-height: calc(100% - 150px);
        }
    </style>

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap"
        rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="assets/darkpan-1.0.0/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="assets/darkpan-1.0.0/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="assets/darkpan-1.0.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="assets/darkpan-1.0.0/css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-fluid position-relative d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner"
            class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Yükleniyor...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3 d-flex flex-column">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="Mainpage.php" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-user-edit me-2"></i>Tickets</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="assets/darkpan-1.0.0/img/user.jpg" alt=""
                            style="width: 40px; height: 40px;">
                        <div
                            class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1">
                        </div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0">
                            <?php echo htmlspecialchars($_SESSION['user_data']['NAME']);
                            echo " ";
                            echo htmlspecialchars($_SESSION['user_data']['SURNAME']); ?>
                        </h6>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="Mainpage.php" class="nav-item nav-link"><i
                            class="fa fa-tachometer-alt me-2"></i>Anasayfa</a>
                    <a href="MyRequests.php" class="nav-item nav-link active"><i
                            class="fa fa-th me-2"></i>Taleplerim</a>
                    <a href="CreateRequest.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Talep Oluştur</a>
                </div>
            </nav>
            <div class="mt-auto">
                <a href="index.php" class="btn btn-danger w-100"><i class="fa fa-sign-out-alt me-2"></i>Çıkış Yap</a>
            </div>
        </div>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content d-flex flex-column flex-grow-1">
            <div class="container-fluid d-flex flex-column h-100">
                <!-- Mesaj Paneli -->
                <div class="row g-4 flex-grow-1 overflow-auto">
                    <div class="col-12">
                        <div class="bg-secondary rounded p-4 h-100">
                            <h6 class="mb-4">Mesajlar</h6>
                            <div id="messagePanel" class="overflow-auto h-100">
                                <?php foreach ($messages as $message): ?>
                                    <div class="message bg-dark text-white p-2 rounded mb-2">
                                        <strong class="d-block text-info">
                                            <?php echo htmlspecialchars($message['NAME']);
                                            echo " ";
                                            echo htmlspecialchars($message['SURNAME']); ?>:
                                        </strong>
                                        <span>
                                            <?php echo htmlspecialchars($message['MESSAGE']); ?>
                                        </span>
                                        <small class="text-muted d-block">
                                            <?php echo htmlspecialchars($message['MSG_DATE']); ?>
                                        </small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mesaj Yazma Alanı -->
                <div class="row g-4">
                    <div class="col-12">
                        <div class="bg-secondary rounded p-4">
                            <div class="form-floating mb-3">
                                <textarea class="form-control" placeholder="Mesajınızı buraya yazın..."
                                    id="messageTextarea" style="height: 100px;"></textarea>
                                <label for="messageTextarea">Mesaj</label>
                            </div>
                            <button class="btn btn-primary w-100" id="sendMessageBtn">Gönder</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Content End -->

        <!-- JavaScript Libraries -->
        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="assets/darkpan-1.0.0/lib/chart/chart.min.js"></script>
        <script src="assets/darkpan-1.0.0/lib/easing/easing.min.js"></script>
        <script src="assets/darkpan-1.0.0/lib/waypoints/waypoints.min.js"></script>
        <script src="assets/darkpan-1.0.0/lib/owlcarousel/owl.carousel.min.js"></script>
        <script src="assets/darkpan-1.0.0/lib/tempusdominus/js/moment.min.js"></script>
        <script src="assets/darkpan-1.0.0/lib/tempusdominus/js/moment-timezone.min.js"></script>
        <script src="assets/darkpan-1.0.0/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

        <!-- Template Javascript -->
        <script src="assets/darkpan-1.0.0/js/main.js"></script>

        <script>
            document.getElementById('sendMessageBtn').addEventListener('click', function() {
                const message = document.getElementById('messageTextarea').value.trim();
                const requestId = <?php echo json_encode($requestId); ?>;
                const senderName = "<?php echo htmlspecialchars($_SESSION['user_data']['NAME'] . ' ' . $_SESSION['user_data']['SURNAME']); ?>";

                if (message === '') {
                    alert('Mesaj boş olamaz.');
                    return;
                }

                fetch('api/send_message_api.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `request_id=${requestId}&message=${encodeURIComponent(message)}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const messagePanel = document.getElementById('messagePanel');
                            const newMessage = document.createElement('div');
                            newMessage.className = 'message bg-dark text-white p-2 rounded mb-2';
                            newMessage.innerHTML = `
                    <strong class="d-block text-info">${senderName}:</strong>
                    <span>${message}</span>
                    <small class="text-muted d-block">Şimdi</small>
                `;
                            messagePanel.appendChild(newMessage);
                            messagePanel.scrollTop = messagePanel.scrollHeight;

                            document.getElementById('messageTextarea').value = '';
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(err => {
                        console.error('Hata:', err);
                        alert('Mesaj gönderilemedi.');
                    });
            });
        </script>
</body>

</html>