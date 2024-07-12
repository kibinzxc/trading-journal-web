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

// Calculate percentage
if ($row['net_gain_loss'] != 0 && $row['margin'] != 0) {
    $percentage = ($row['net_gain_loss'] / $row['margin']) * 100;
} else {
    $percentage = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Trade</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 20px;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: auto;
            display: grid;
            gap: 20px;
        }
        .row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }
        .column {
            padding: 10px;
            border-right: 1px solid #ccc;
        }
        .column:last-child {
            border-right: none;
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        p {
            margin-bottom: 10px;
            line-height: 1.6;
        }
        .quote {
            padding: 20px;
            margin: 20px 0;
            border-left: 5px solid #eee;
            background-color: #f9f9f9;
            font-style: italic;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- First Row: Trade Dates, Details, and Performance -->
        <div class="row">
            <div class="column">
                <h2>Trade Dates</h2>
                <?php
                // Assuming $row['open_date_time'] and $row['close_date_time'] are in 'Y-m-d H:i:s' format
                $openDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $row['open_date_time']);
                $closeDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $row['close_date_time']);

                $openDateFormatted = $openDateTime ? $openDateTime->format('F j, Y g:i A') : 'N/A';
                $closeDateFormatted = $closeDateTime && $row['close_date_time'] != '0000-00-00 00:00:00' ? $closeDateTime->format('F j, Y g:i A') : 'N/A';
                ?>

                <p><strong>Open:</strong> <?php echo $openDateFormatted; ?></p>
                <?php if (!empty($row['close_date_time']) && $row['close_date_time'] != '0000-00-00 00:00:00'): ?>
                    <p><strong>Close: </strong> <?php echo $closeDateFormatted; ?></p>
                <?php endif; ?>
            </div>
            <div class="column">
                <h2>Trade Details</h2>
                <p><strong>Symbol:</strong> <?php echo $row['symbol']; ?></p>
                <p><strong>Direction:</strong> <?php
                // Determine the color based on the direction
                if ($row["direction"] == "Long") {
                    $directionColor = '#19692c'; // Green for Long
                } elseif ($row["direction"] == "Short") {
                    $directionColor = '#a71d2a'; // Red for Short
                } else {
                    $directionColor = '#000000'; // Default to black if neither
                }

                // Output the direction with the determined color
                echo "<span style='color: " . $directionColor . ";'><strong>" . $row['direction'] . "</strong></span>";
                ?></p>
                <p><strong>Leverage:</strong> <?php echo $row['leverage']; ?></p>
            </div>
            <div class="column">
                <h2>Performance</h2>
                <p><strong>Margin:</strong> <?php echo $row['margin']; ?></p>
                <?php
                // Color coding for Net Gain/Loss
                $netGainLossColor = '#000000'; // Default to black
                if ($row["net_gain_loss"] < 0) {
                    $netGainLossColor = '#a71d2a'; // Darker red if below 0
                } elseif ($row["net_gain_loss"] > 0) {
                    $netGainLossColor = '#19692c'; // Darker green if above 0
                }
                ?>
                <p><strong>Net Gain/Loss:</strong> <span style="color: <?php echo $netGainLossColor; ?>;"><strong><?php echo $row["net_gain_loss"]; ?></strong></span></p>
                <?php
                // Color coding for Percentage
                $percentageColor = '#000000'; // Default to black
                if ($percentage < 0) {
                    $percentageColor = '#a71d2a'; // Darker red if below 0
                } elseif ($percentage > 0) {
                    $percentageColor = '#19692c'; // Darker green if above 0
                }
                ?>
                <p><strong>Percentage:</strong> <span style="color: <?php echo $percentageColor; ?>;"><strong><?php echo number_format($percentage, 2); ?>%</strong></span></p>
            </div>
        </div>

        <!-- Second Row: Strategy -->
        <div class="row">
            <div class="column" style="grid-column: span 3;text-align:center;">
                <h2>Strategy</h2>
                <div class="quote"><?php echo htmlspecialchars($row['strategy'], ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        </div>

        <!-- Third Row: Images -->
        <div class="row">
            <?php if (!empty($row['image']) || !empty($row['closing_image'])): ?>
                <div class="column" style="grid-column: span 3; text-align:center;">
                    <?php if (!empty($row['image'])): ?>
                        <h2>Trade Image</h2>
                        <img src="<?php echo $row['image']; ?>" alt="Trade Image" width="200">
                    <?php endif; ?>
                </div>
        </div>
        <div class="row">
                <div class="column" style="grid-column: span 3;text-align:center;" >
                    <?php if (!empty($row['closing_image'])): ?>
                        <h2>Closing Image</h2>
                        <img src="<?php echo $row['closing_image']; ?>" alt="Closing Image" width="200">
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Fourth Row: Remarks -->
        <?php if (!empty($row['remarks'])): ?>
            <div class="row">
                <div class="column" style="grid-column: span 3;text-align:center;">
                    <h2>Remarks</h2>
                    <div class="quote"><?php echo htmlspecialchars($row['remarks'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>
