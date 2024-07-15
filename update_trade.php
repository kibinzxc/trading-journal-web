<?php
date_default_timezone_set('Asia/Manila');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "trading_journal";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_POST['id'];
$net_gain_loss = $_POST['net_gain_loss'];
$remarks = $_POST['remarks'];

// Sanitize inputs if needed (depending on your validation strategy)
$id = intval($id); // Assuming id is an integer

// Check if a new image file is uploaded
if (!empty($_FILES['closing_image']['name'])) {
    $closing_image = $_FILES['closing_image']['name'];

    // File upload handling for closing image
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($closing_image);
    move_uploaded_file($_FILES['closing_image']['tmp_name'], $target_file);

    // Update closing image in database
    $sql = "UPDATE trades SET net_gain_loss='$net_gain_loss', remarks='$remarks', closing_image='$target_file' WHERE id=$id";
} else {
    // Retain existing closing image in database
    $sql = "UPDATE trades SET net_gain_loss='$net_gain_loss', remarks='$remarks' WHERE id=$id";
}

// Handle setting close_date_time if checkbox is checked
if (isset($_POST['close_trade'])) {
    $close_date_time = date('Y-m-d H:i:s');
    $sql = "UPDATE trades SET close_date_time='$close_date_time' WHERE id=$id";
}

// Update total_amount
$sql2 = "UPDATE total_amount SET total_amount = total_amount + $net_gain_loss WHERE amountID = 1";

if ($conn->query($sql2) === FALSE) {
    echo "Error updating total amount: " . $conn->error;
    $conn->close();
    exit();
}

if ($conn->query($sql) === TRUE) {
    $conn->close();
    echo '<script>window.opener.location.href = "index.php?update=success"; window.close();</script>';
    exit();
} else {
    echo "Error updating trade: " . $conn->error;
    $conn->close();
    exit();
}
?>
