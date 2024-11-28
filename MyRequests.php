<?php
session_start();

if (!isset($_SESSION['user_data'])) {
    header("Location: index.php");
    exit;
}

include('includes/conn.php');

$userId = $_SESSION['user_data']['ID'];
$userGroupId = $_SESSION['user_data']['UG_ID'];

if ($userGroupId == 1) {
    $sql = "SELECT 
                R.ID AS REQUEST_ID, 
                R.TOPIC, 
                R.DESCRIPTION, 
                R.CATEGORY, 
                R.STATUS, 
                U.NAME, 
                U.SURNAME, 
                UG.GROUP_NAME
            FROM MTH_REQUESTS R
            JOIN MTH_USERS U ON R.U_ID = U.ID
            JOIN MTH_USERS_GROUP UG ON U.UG_ID = UG.ID";
} else if ($userGroupId == 2) {
    $sql = "SELECT 
                R.ID AS REQUEST_ID, 
                R.TOPIC, 
                R.DESCRIPTION, 
                R.CATEGORY, 
                R.STATUS, 
                U.NAME, 
                U.SURNAME, 
                UG.GROUP_NAME
            FROM MTH_REQUESTS R
            JOIN MTH_USERS U ON R.U_ID = U.ID
            JOIN MTH_USERS_GROUP UG ON U.UG_ID = UG.ID
            WHERE U.ID = :user_id";
}

$stmt = oci_parse($conn, $sql);

if ($userGroupId == 2) {
    oci_bind_by_name($stmt, ':user_id', $userId);
}

oci_execute($stmt);

$requests = [];
while ($row = oci_fetch_assoc($stmt)) {
    $requests[] = $row;
}

oci_free_statement($stmt);
oci_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Taleplerim</title>
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

    <!-- Libraries Stylesheet -->
    <link href="assets/darkpan-1.0.0/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="assets/darkpan-1.0.0/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="assets/darkpan-1.0.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="assets/darkpan-1.0.0/css/style.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>

<body>
    <div class="container-fluid position-relative d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-dark position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
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
                    <a href="MyRequests.php" class="nav-item nav-link active"><i class="fa fa-th me-2"></i>Taleplerim</a>
                    <a href="CreateRequest.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Talep Oluştur</a>
                </div>
            </nav>
            <div class="mt-auto">
                <a href="index.php" class="btn btn-danger w-100"><i class="fa fa-sign-out-alt me-2"></i>Çıkış Yap</a>
            </div>
        </div>
        <!-- Sidebar End -->
        <!-- Content Start -->
        <div class="content">
            <!-- Table Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <div class="col-12">
                        <div class="bg-secondary rounded h-100 p-4">
                            <h6 class="mb-4">Taleplerim</h6>
                            <div class="table-responsive">
                                <table class="table" id="myTable">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Talep ID</th>
                                            <th scope="col">Ad</th>
                                            <th scope="col">Soyad</th>
                                            <th scope="col">Konu</th>
                                            <th scope="col">Kategori</th>
                                            <th scope="col">Açıklama</th>
                                            <th scope="col">Durum</th>
                                            <th scope="col">Mesajlar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($requests as $key => $request): ?>
                                            <tr>
                                                <th scope="row"><?php echo $key + 1; ?></th>
                                                <td><?php echo htmlspecialchars($request['REQUEST_ID']); ?></td>
                                                <td><?php echo htmlspecialchars($request['NAME']); ?></td>
                                                <td><?php echo htmlspecialchars($request['SURNAME']); ?></td>
                                                <td><?php echo htmlspecialchars($request['TOPIC']); ?></td>
                                                <td><?php echo htmlspecialchars($request['CATEGORY']); ?></td>
                                                <td><?php echo htmlspecialchars($request['DESCRIPTION']); ?></td>
                                                <td>
                                                    <?php if ($request['STATUS'] === 'Devam Ediyor'): ?>
                                                        <span class="badge bg-success"><?php echo htmlspecialchars($request['STATUS']); ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger"><?php echo htmlspecialchars($request['STATUS']); ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="MyMessages.php?request_id=<?php echo htmlspecialchars($request['REQUEST_ID']); ?>" class="btn btn-primary btn-sm">Detay</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Table End -->
        </div>
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
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- Template Javascript -->
    <script src="assets/darkpan-1.0.0/js/main.js"></script>

    <!-- Datatables -->
    <script>
        $(document).ready(function() {
            $('#myTable').DataTable({
                "paging": true,
                "searching": true,
                "info": true,
                "ordering": true,
                "lengthMenu": [5, 10, 25, 50],
                "language": {
                    "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/tr.json"
                }
            });
        });
    </script>

</body>

</html>