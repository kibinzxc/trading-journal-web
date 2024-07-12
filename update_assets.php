<?php
// Set the timezone to Philippines (PHT)
date_default_timezone_set('Asia/Manila');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "trading_journal";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to insert asset into assets table and update total_amount
function insertAsset($conn, $transactionType, $amount) {
    // Prepare SQL statement for insertion into assets table
    $sql_insert_asset = "INSERT INTO recent_activity(type, amount) VALUES (?, ?)";
    $stmt_insert_asset = $conn->prepare($sql_insert_asset);

    if ($stmt_insert_asset === false) {
        die('MySQL prepare error: ' . htmlspecialchars($conn->error));
    }

    // Bind parameters and execute insertion statement
    $stmt_insert_asset->bind_param('sd', $transactionType, $amount);
    if (!$stmt_insert_asset->execute()) {
        die('MySQL execute error: ' . htmlspecialchars($stmt_insert_asset->error));
    }

    $stmt_insert_asset->close();

    // Update total_amount based on transaction_type
    if ($transactionType === 'deposit') {
        $sql_update_total = "UPDATE total_amount SET total_amount = total_amount + ? WHERE amountID = 1"; // Assuming id 1 is for total_amount
    } elseif ($transactionType === 'withdrawal') {
        $sql_update_total = "UPDATE total_amount SET total_amount = total_amount - ? WHERE amountID = 1"; // Assuming id 1 is for total_amount
    }

    $stmt_update_total = $conn->prepare($sql_update_total);
    if ($stmt_update_total === false) {
        die('MySQL prepare error: ' . htmlspecialchars($conn->error));
    }

    // Bind parameter and execute update statement
    $stmt_update_total->bind_param('d', $amount);
    if (!$stmt_update_total->execute()) {
        die('MySQL execute error: ' . htmlspecialchars($stmt_update_total->error));
    }

    $stmt_update_total->close();
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $transactionType = $_POST['transaction_type'];
    $amount = $_POST['amount'];

    // Call function to insert asset into assets table and update total_amount
    insertAsset($conn, $transactionType, $amount);

    // Redirect or display success message
    echo '<script>window.opener.location.href = "dashboard.php?add=success"; window.close();</script>';
    exit();
}

// Close database connection
$conn->close();
?>
