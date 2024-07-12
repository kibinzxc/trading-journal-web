<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "trading_journal";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect POST data
$symbol = $_POST['symbol'];
$direction = $_POST['direction'];
$margin = $_POST['margin'];
$leverage = $_POST['leverage'];
$rrr = $_POST['rrr'];
$strategy = $_POST['strategy']; // New field: Strategy

// File upload handling
$image = $_FILES['image']['name'];
$target_dir = "uploads/";
$target_file = $target_dir . basename($image);
move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

// Prepare SQL statement
$sql = "INSERT INTO trades (symbol, direction, margin, leverage, rrr, image, strategy)
        VALUES ('$symbol', '$direction', '$margin', '$leverage', '$rrr', '$target_file', '$strategy')";

// Execute SQL statement
if ($conn->query($sql) === TRUE) {
    $conn->close();

    // Send success signal to parent window
    echo '<script>window.opener.location.href = "index.php?add=success"; window.close();</script>';
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>
