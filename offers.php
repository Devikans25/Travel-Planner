<?php
// Database connection
$host = "localhost"; // Change if needed
$user = "root"; // Change if needed
$pass = ""; // Change if needed
$dbname = "travel_offers";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch travel package offers from database
$sql = "SELECT title, description, icon FROM travel_packages";
$result = $conn->query($sql);

$offers = [];

if ($result) {  // Check if query was successful
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $offers[] = $row;
        }
    }
} else {
    die("Query failed: " . $conn->error);  // Display the actual error
}


// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Packages</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file here -->
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .container { max-width: 900px; margin: 20px auto; text-align: center; }
        .offer-box { background: #fff; padding: 20px; margin: 10px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .offer-box i { font-size: 40px; color: #ff5722; }
        .offer-box h3 { margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Exclusive Travel Packages</h2>
        <?php if (!empty($offers)) : ?>
            <?php foreach ($offers as $offer) : ?>
                <div class="offer-box">
                    <i class="fa <?php echo htmlspecialchars($offer['icon']); ?>"></i>
                    <h3><?php echo htmlspecialchars($offer['title']); ?></h3>
                    <p><?php echo htmlspecialchars($offer['description']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>No travel packages available at the moment.</p>
        <?php endif; ?>
    </div>
</body>
</html>
