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
    <h2>Update Assets</h2>
    <form action="update_assets.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">

        <label for="transaction_type">Transaction Type:</label>
        <select name="transaction_type" required>
            <option value="withdrawal">Withdrawal</option>
            <option value="deposit" >Deposit</option>
        </select>

        <label for="amount">Amount:</label>
        <input type="number" step="0.01" name="amount" value="<?php echo $row['amount']; ?>" required>

        <button type="submit">Update Assets</button>
    </form>
</div>
</body>
</html>
