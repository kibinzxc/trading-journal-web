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

$id = $_GET['id'];

// Fetch trade details from database
$sql = "SELECT * FROM trades WHERE id = $id";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    die("Error: Trade not found");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Trade</title>
   <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin: 10px 0 5px;
            color: #555;
        }
        input, textarea, select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 16px;
        }
        input[type="file"] {
            padding: 3px;
        }
        button {
            background-color: #006D6D;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #004D4D;
        }
        .checkbox-container {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        .checkbox-container input {
            margin-right: 10px;
        }
        .current-image img {
            width: 100px;
            height: 100px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Trade</h2>
        <form action="update_trade.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

            <label for="net_gain_loss">Net Gain/Loss:</label>
            <input type="number" step="0.01" name="net_gain_loss" value="<?php echo $row['net_gain_loss']; ?>" required>

            <label for="remarks">Remarks:</label>
            <textarea name="remarks" required><?php echo $row['remarks']; ?></textarea>

            <label for="closing_image">Closing Image:</label>
            <input type="file" name="closing_image" accept="image/*">
            <!-- Hidden input to store current image path/name -->
            <input type="hidden" name="current_closing_image" value="<?php echo $row['closing_image']; ?>">

            <?php if (!empty($row['closing_image'])): ?>
                <div class="current-image">
                    <p>Current Image:</p>
                    <img src="<?php echo $row['closing_image']; ?>" alt="Current Closing Image">
                </div>
            <?php endif; ?>

            <div class="checkbox-container">
                <input type="checkbox" id="close_trade" name="close_trade">
                <label for="close_trade">Trade has been already closed</label>
            </div>

            <!-- Hidden input field for close_date_time -->
            <input type="hidden" id="close_date_time" name="close_date_time" value="<?php echo isset($row['close_date_time']) ? $row['close_date_time'] : ''; ?>">

            <button type="submit">Update Trade</button>
        </form>
    </div>
</body>
</html>
