<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "trading_journal";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Collect POST data
$symbol = $_POST['symbol'];
$direction = $_POST['direction'];
$margin = $_POST['margin'];
$leverage = $_POST['leverage'];
$net_gain_loss = isset($_POST['net_gain_loss']) && $_POST['net_gain_loss'] !== '' ? $_POST['net_gain_loss'] : 0; // Set net_gain_loss to 0 if empty
$remarks = $_POST['remarks'];

// Calculate percentage
$percentage = ($net_gain_loss / $margin) * 100;

// File upload handling
$image = $_FILES['image']['name'];
$target_dir = "uploads/";
$target_file = $target_dir . basename($image);
move_uploaded_file($_FILES['image']['tmp_name'], $target_file);

// Prepare SQL statement
$sql = "INSERT INTO trades (symbol, direction, margin, leverage, net_gain_loss, percentage, image, remarks)
        VALUES ('$symbol', '$direction', '$margin', '$leverage', '$net_gain_loss', '$percentage', '$target_file', '$remarks')";

// Execute SQL statement
if ($conn->query($sql) === TRUE) {
    header("Location: index.php?status=success");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close connection
$conn->close();
?>
