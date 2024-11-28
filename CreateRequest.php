<?php
session_start();

if (!isset($_SESSION['user_data'])) {
    header("Location: index.php");
    exit;
}

$conn = oci_connect('YZLM_OLD', 'BYZYZ', 'BYZDB', "AL32UTF8");
if (!$conn) {
    die("Veritabanına bağlanılamadı.");
}

$userId = $_SESSION['user_data']['ID'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $topic = $_POST['topic'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    $sql = "INSERT INTO MTH_REQUESTS (TOPIC, CATEGORY, DESCRIPTION, U_ID, STATUS) 
            VALUES (:topic, :category, :description, :user_id, 'Devam Ediyor')";

    $stmt = oci_parse($conn, $sql);

    oci_bind_by_name($stmt, ':topic', $topic);
    oci_bind_by_name($stmt, ':category', $category);
    oci_bind_by_name($stmt, ':description', $description);
    oci_bind_by_name($stmt, ':user_id', $userId);

    if (oci_execute($stmt)) {
        header("Location: MyRequests.php");
        exit;
    } else {
        echo "Talep oluşturulurken bir hata oluştu.";
    }

    oci_free_statement($stmt);
}

oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Talep Oluştur</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="assets/darkpan-1.0.0/img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Roboto:wght@500;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="assets/darkpan-1.0.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="assets/darkpan-1.0.0/css/style.css" rel="stylesheet">

</head>

<body>
    <div class="container-fluid position-relative d-flex p-0">
        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3 d-flex flex-column">
            <nav class="navbar bg-secondary navbar-dark">
                <a href="Mainpage.html" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-user-edit me-2"></i>Tickets</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="assets/darkpan-1.0.0/img/user.jpg" alt="" style="width: 40px; height: 40px;">
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0"><?php echo htmlspecialchars($_SESSION['user_data']['NAME']);
                                            echo " ";
                                            echo htmlspecialchars($_SESSION['user_data']['SURNAME']); ?></h6>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="Mainpage.php" class="nav-item nav-link"><i class="fa fa-tachometer-alt me-2"></i>Anasayfa</a>
                    <a href="MyRequests.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Taleplerim</a>
                    <a href="CreateRequest.php" class="nav-item nav-link active"><i class="fa fa-th me-2"></i>Talep Oluştur</a>
                </div>
            </nav>
            <div class="mt-auto">
                <a href="index.php" class="btn btn-danger w-100"><i class="fa fa-sign-out-alt me-2"></i>Çıkış Yap</a>
            </div>
        </div>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content">
            <div class="form-container">
                <div class="bg-secondary rounded p-4">
                    <h6 class="mb-4">Talep Formu</h6>
                    <form method="POST" onsubmit="return handleSubmit(event)">
                        <div class="mb-3">
                            <label for="topic" class="form-label">Konu</label>
                            <input type="text" class="form-control" id="topic" name="topic" required>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Kategori</label>
                            <select class="form-control" id="category" name="category" required>
                                <option value="Teknik">Teknik</option>
                                <option value="Satınalma">Satınalma</option>
                                <option value="Lojistik">Lojistik</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Talep Oluştur</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Content End -->
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/darkpan-1.0.0/js/main.js"></script>

    <script>
        function handleSubmit(event) {
            event.preventDefault();

            alert("Ekleme başarılı!");
            event.target.submit();

            return false;
        }
    </script>
</body>

</html>