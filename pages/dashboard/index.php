<?php
include '../../auth/auth_check.php';
require_once '../../config/config.php';

$page = 'dashboard';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard | AleMart</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- CSS Custom -->
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>
    <?php
    include '../../includes/navbar.php';
    include '../../includes/sidebar.php';
    ?>

    <div class="main-content">

        <h1>Dashboard</h1>
    </div>

    <?php
    include '../../includes/footer.php';
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/script.js"></script>
</body>

</html>