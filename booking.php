<?php
session_start();
include('includes/config.php');
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Validate package ID
$pkgid = filter_input(INPUT_GET, 'pkgid', FILTER_VALIDATE_INT);
if (!$pkgid) {
    echo "<script>alert('Invalid Package ID'); window.location.href='index.php';</script>";
    exit;
}

// Fetch package details
$sql = "SELECT PackageName, PackagePrice FROM tbltourpackages WHERE PackageId = :pkgid";
$query = $dbh->prepare($sql);
$query->bindParam(':pkgid', $pkgid, PDO::PARAM_INT);
$query->execute();
$package = $query->fetch(PDO::FETCH_OBJ);

if (!$package) {
    echo "<script>alert('Package not found'); window.location.href='index.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = htmlentities($_POST['name']);
    $email = htmlentities($_POST['email']);
    $phone = htmlentities($_POST['phone']);
    $message = htmlentities($_POST['message']);

    // Insert booking into database
    $sql = "INSERT INTO bookings (PackageId, Name, Email, Phone, Message, BookingDate) 
            VALUES (:pkgid, :name, :email, :phone, :message, NOW())";
    $query = $dbh->prepare($sql);
    $query->bindParam(':pkgid', $pkgid, PDO::PARAM_INT);
    $query->bindParam(':name', $name, PDO::PARAM_STR);
    $query->bindParam(':email', $email, PDO::PARAM_STR);
    $query->bindParam(':phone', $phone, PDO::PARAM_STR);
    $query->bindParam(':message', $message, PDO::PARAM_STR);

    if ($query->execute()) {
        // Send confirmation email
        $mail = new PHPMailer(true);
        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'devuniru222@gmail.com';  // Replace with your Gmail
            $mail->Password = 'tkhq aamv pvmd ivmh';  // Replace with App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email settings
            $mail->setFrom('devikans2508@gmail.com', 'Travel Agency');
            $mail->addAddress($email, $name);
            $mail->isHTML(true);
            $mail->Subject = 'Booking Confirmation - ' . htmlentities($package->PackageName);
            $mail->Body = "
                <h2>Booking Confirmation</h2>
                <p>Dear <b>$name</b>,</p>
                <p>Thank you for booking the <b>{$package->PackageName}</b>.</p>
                <p><strong>Price:</strong> Rs. {$package->PackagePrice}</p>
                <p>We will contact you soon with further details.</p>
                <br>
                <p>Best Regards,</p>
                <p>Travel Company</p>
            ";

            $mail->send();
            echo "<script>alert('Booking Successful! A confirmation email has been sent.'); window.location.href='index.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Booking Successful, but email could not be sent. Error: {$mail->ErrorInfo}'); window.location.href='index.php';</script>";
        }
    } else {
        echo "<script>alert('Error in booking. Try again.');</script>";
    }
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Book Package</title>
    <link href="css/bootstrap.css" rel='stylesheet' type='text/css' />
    <link href="css/style.css" rel='stylesheet' type='text/css' />
</head>
<body>
    <?php include('includes/header.php'); ?>
    <div class="container">
        <br><h2>Book: <?php echo htmlentities($package->PackageName); ?></h2>
        <br><h4>Price: Rs. <?php echo htmlentities($package->PackagePrice); ?></h4>

        <form method="post">
            <div class="form-group">
                <br><label>Your Name:</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Phone:</label>
                <input type="text" name="phone" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Message:</label>
                <textarea name="message" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-success">Confirm Booking</button>
        </form>
    </div>
    <br><?php include('includes/footer.php'); ?>
</body>
</html>
