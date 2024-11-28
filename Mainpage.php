<?php
session_start();

if (!isset($_SESSION['user_data'])) {
    header("Location: index.php");
    exit;
}

include('includes/conn.php');

$userId = $_SESSION['user_data']['ID'];
$userGroupId = $_SESSION['user_data']['UG_ID'];

$totalRequests = $openRequests = $closedRequests = 0;
$categoryData = [];

if ($userGroupId == 1) {
    $sql = "SELECT 
                COUNT(*) AS TOTAL, 
                SUM(CASE WHEN STATUS = 'Devam Ediyor' THEN 1 ELSE 0 END) AS OPEN, 
                SUM(CASE WHEN STATUS = 'Kapalı' THEN 1 ELSE 0 END) AS CLOSED 
            FROM MTH_REQUESTS";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);

    if ($row = oci_fetch_assoc($stmt)) {
        $totalRequests = $row['TOTAL'];
        $openRequests = $row['OPEN'];
        $closedRequests = $row['CLOSED'];
    }

    $categorySql = "SELECT CATEGORY, COUNT(*) AS COUNT
                    FROM MTH_REQUESTS
                    GROUP BY CATEGORY";
    $categoryStmt = oci_parse($conn, $categorySql);
    oci_execute($categoryStmt);

    while ($categoryRow = oci_fetch_assoc($categoryStmt)) {
        $categoryData[] = $categoryRow;
    }

    oci_free_statement($categoryStmt);
} else if ($userGroupId == 2) {
    $sql = "SELECT 
                COUNT(*) AS TOTAL, 
                SUM(CASE WHEN STATUS = 'Devam Ediyor' THEN 1 ELSE 0 END) AS OPEN, 
                SUM(CASE WHEN STATUS = 'Kapalı' THEN 1 ELSE 0 END) AS CLOSED 
            FROM MTH_REQUESTS 
            WHERE U_ID = :userId";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ':userId', $userId);
    oci_execute($stmt);

    if ($row = oci_fetch_assoc($stmt)) {
        $totalRequests = $row['TOTAL'];
        $openRequests = $row['OPEN'];
        $closedRequests = $row['CLOSED'];
    }

    $categorySql = "SELECT CATEGORY, COUNT(*) AS COUNT
                    FROM MTH_REQUESTS
                    WHERE U_ID = :userId
                    GROUP BY CATEGORY";
    $categoryStmt = oci_parse($conn, $categorySql);
    oci_bind_by_name($categoryStmt, ':userId', $userId);
    oci_execute($categoryStmt);

    while ($categoryRow = oci_fetch_assoc($categoryStmt)) {
        $categoryData[] = $categoryRow;
    }

    oci_free_statement($categoryStmt);
}

oci_free_statement($stmt);
oci_close($conn);

$categoryDataJson = json_encode($categoryData);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Anasayfa</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="assets/darkpan-1.0.0/img/favicon.ico" rel="icon">

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #1e1e2f;
            /* Çubuk grafik arka planına uyumlu */
        }

        .chart-container {
            width: 400px;
            text-align: center;
        }

        canvas {
            margin: 20px auto;
        }

        .chart-title {
            font-size: 18px;
            font-weight: bold;
            color: #ffffff;
            /* Beyaz metin rengi */
        }
    </style>
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
                        <h6 class="mb-0"><?php echo htmlspecialchars($_SESSION['user_data']['NAME']);
                                            echo " ";
                                            echo htmlspecialchars($_SESSION['user_data']['SURNAME']); ?></h6>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="Mainpage.php" class="nav-item nav-link active"><i
                            class="fa fa-tachometer-alt me-2"></i>Anasayfa</a>
                    <a href="MyRequests.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Taleplerim</a>
                    <a href="CreateRequest.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Talep Oluştur</a>
                </div>
            </nav>
            <div class="mt-auto">
                <button id="logoutButton" class="btn btn-danger w-100">
                    <i class="fa fa-sign-out-alt me-2"></i>Çıkış Yap
                </button>
            </div>
        </div>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content">
            <!-- Sale & Revenue Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-chart-bar fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Toplam Talep Sayısı</p>
                                <h6 class="mb-0"><?php echo $totalRequests; ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-chart-area fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Açık Talep Sayısı</p>
                                <h6 class="mb-0"><?php echo $openRequests; ?></h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-secondary rounded d-flex align-items-center justify-content-between p-4">
                            <i class="fa fa-chart-pie fa-3x text-primary"></i>
                            <div class="ms-3">
                                <p class="mb-2">Kapalı Talep Sayısı</p>
                                <h6 class="mb-0"><?php echo $closedRequests; ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sale & Revenue End -->

            <!-- Pie Chart Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <!-- Pie Chart -->
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-secondary text-center rounded p-4">
                            <div class="chart-container">
                                <div class="chart-title">Kategorilere Göre Talep Dağılımı</div>
                                <canvas id="ticketsChart"></canvas>
                            </div>
                            <script>
                                const categoryData = <?php echo $categoryDataJson; ?>;

                                const labels = categoryData.map(item => item.CATEGORY);
                                const values = categoryData.map(item => parseInt(item.COUNT));

                                const ctx = document.getElementById('ticketsChart').getContext('2d');
                                const ticketsChart = new Chart(ctx, {
                                    type: 'pie',
                                    data: {
                                        labels: labels,
                                        datasets: [{
                                            label: 'Tickets',
                                            data: values,
                                            backgroundColor: ['#D32F2F', '#FF6F00', '#512DA8'],
                                            borderColor: ['#1e1e2f', '#1e1e2f', '#1e1e2f'],
                                            borderWidth: 2
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        plugins: {
                                            legend: {
                                                position: 'right',
                                                labels: {
                                                    usePointStyle: true,
                                                    color: '#ffffff'
                                                }
                                            },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        return `${context.raw}`;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                            </script>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Sales Chart End -->
            <!-- Content End -->
        </div>

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
            document.getElementById("logoutButton").addEventListener("click", function() {
                window.location.href = "api/logout_api.php";
            });
        </script>
</body>

</html>