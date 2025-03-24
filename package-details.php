<?php
session_start();
include('includes/config.php');

$pkgid = filter_input(INPUT_GET, 'pkgid', FILTER_VALIDATE_INT);
if (!$pkgid) {
    echo "<script>alert('Invalid Package ID'); window.location.href='index.php';</script>";
    exit;
}

$sql = "SELECT * FROM tbltourpackages WHERE PackageId = :pkgid";
$query = $dbh->prepare($sql);
$query->bindParam(':pkgid', $pkgid, PDO::PARAM_INT);
$query->execute();

if ($query->rowCount() > 0) {
    $result = $query->fetch(PDO::FETCH_OBJ);
} else {
    echo "<script>alert('Package not found'); window.location.href='index.php';</script>";
    exit;
}

$packageImage = !empty($result->PackageImage) ? htmlentities($result->PackageImage) : 'wayanad.jpg';
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Package Details</title>
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
    <link href="css/style.css" rel='stylesheet' type='text/css' />
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="container">
    <br><h2><?php echo htmlentities($result->PackageName); ?></h2><br>
    <img src="images/<?php echo $packageImage; ?>" class="img-responsive" alt="Package Image">
    <br><h4>Package Type: <?php echo htmlentities($result->PackageType); ?></h4>
    
    <p><b>Features:</b></p>
    <ul>
        <li><b>Hotel:</b> <?php echo htmlentities($result->HotelDetails ?? 'Not specified'); ?></li>
        <li><b>Transport:</b> <?php echo htmlentities($result->TransportDetails ?? 'Not specified'); ?></li>
        <li><b>Meals:</b> <?php echo htmlentities($result->MealPlan ?? 'Not specified'); ?></li>
        <li><b>Sightseeing:</b> <?php echo nl2br(htmlspecialchars_decode($result->SightseeingDetails ?? 'Not available')); ?></li>
    </ul>

    <p><b>Details:</b> <?php echo htmlentities($result->PackageDetails); ?></p>
    <h5>Price: Rs. <?php echo htmlentities($result->PackagePrice); ?></h5>
    
    <br><a href="booking.php?pkgid=<?php echo htmlentities($result->PackageId); ?>" class="btn btn-primary">Book Now</a>
</div>

    <br><?php include('includes/footer.php'); ?>
</body>
</html>