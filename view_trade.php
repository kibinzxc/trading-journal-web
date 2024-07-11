<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "trading_journal";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];

$sql = "SELECT * FROM trades WHERE id = $id";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Trade</title>
</head>
<body>
    <h2>View Trade</h2>
    <p>Open Date & Time: <?php echo $row['open_date_time']; ?></p>
    <p>Symbol: <?php echo $row['symbol']; ?></p>
    <p>Direction: <?php echo $row['direction']; ?></p>
    <p>Margin: <?php echo $row['margin']; ?></p>
    <p>Leverage: <?php echo $row['leverage']; ?></p>
    <p>Net Gain/Loss: <?php echo $row['net_gain_loss']; ?></p>
    <p>Percentage: <?php echo $row['percentage']; ?></p>
    <p>Image: <img src="<?php echo $row['image']; ?>" alt="Trade Image" width="200"></p>
    <p>Remarks: <?php echo $row['remarks']; ?></p>
</body>
</html>

<?php
$conn->close();
?>
