<?php
session_start();
include(includes/conn)
if (!$conn) {
    $e = oci_error();
    die("Veritabanı bağlantı hatası: " . $e['message']);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Kaydol</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

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

        <!-- Sign Up Start -->
        <div class="container-fluid">
            <div class="row h-100 align-items-center justify-content-center" style="min-height: 100vh;">
                <div class="col-12 col-sm-8 col-md-6 col-lg-5 col-xl-4">
                    <div class="bg-secondary rounded p-4 p-sm-5 my-4 mx-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h3 class="text-primary"><i class="fa fa-user-edit me-2"></i>Tickets</h3>
                            <h3>Kayıt</h3>
                        </div>
                        <form action="api/signup_api.php" method="POST" onsubmit="return handleSubmit(event)">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="floatingText" name="ad" placeholder="Ad" required>
                                <label for="floatingText">Ad</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="floatingText" name="soyad" placeholder="Soyad" required>
                                <label for="floatingText">Soyad</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="floatingInput" name="email" placeholder="E-Posta" required>
                                <label for="floatingInput">E-Posta Adresi</label>
                            </div>
                            <div class="form-floating mb-4">
                                <input type="password" class="form-control" id="floatingPassword" name="sifre" placeholder="Şifre" required>
                                <label for="floatingPassword">Şifre</label>
                            </div>
                            <button type="submit" class="btn btn-primary py-3 w-100 mb-4">Kaydol</button>
                        </form>
                        <p class="text-center mb-0">Hesabın var mı? <a href="index.php">Giriş Yap</a></p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Sign Up End -->
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
        function handleSubmit(event) {
            event.preventDefault();

            alert("Kayıt başarılı!");
            event.target.submit();

            return false;
        }
    </script>
</body>

</html>