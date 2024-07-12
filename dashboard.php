<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "trading_journal";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve total amount and last updated time
$totalAmountQuery = "SELECT total_amount, updated FROM total_amount LIMIT 1";
$totalAmountResult = $conn->query($totalAmountQuery);
$totalAmountData = $totalAmountResult->fetch_assoc();

$totalAmount = $totalAmountData['total_amount'];
$updatedTime = $totalAmountData['updated'];

// Calculate average RRR
$averageRRRQuery = "SELECT AVG(rrr) as average_rrr FROM trades";
$averageRRRResult = $conn->query($averageRRRQuery);
$averageRRRData = $averageRRRResult->fetch_assoc();

$averageRRR = $averageRRRData['average_rrr'];

// Determine the message based on the average RRR
if ($averageRRR < 2) {
    $rrrMessage = "Low Effectiveness";
} elseif ($averageRRR >= 2 && $averageRRR < 4) {
    $rrrMessage = "Moderate Benefit";
} elseif ($averageRRR >= 4 && $averageRRR < 6) {
    $rrrMessage = "Significant Effectiveness";
} else {
    $rrrMessage = "High Benefit";
}

// Calculate profit
$profitQuery = "SELECT SUM(net_gain_loss) as total_profit FROM trades WHERE open_date_time >= '$updatedTime'";
$profitResult = $conn->query($profitQuery);
$profitData = $profitResult->fetch_assoc();

$totalProfit = $profitData['total_profit'];

// Calculate total trades
$totalTradesQuery = "SELECT COUNT(*) AS total_trades FROM trades";
$totalTradesResult = $conn->query($totalTradesQuery);
$totalTradesData = $totalTradesResult->fetch_assoc();

$totalTrades = $totalTradesData['total_trades'];

// Calculate winning trades (positive net_gain_loss)
$winningTradesQuery = "SELECT COUNT(*) AS winning_trades FROM trades WHERE net_gain_loss > 0";
$winningTradesResult = $conn->query($winningTradesQuery);
$winningTradesData = $winningTradesResult->fetch_assoc();

$winningTrades = $winningTradesData['winning_trades'];

// Calculate losing trades (negative net_gain_loss)
$losingTradesQuery = "SELECT COUNT(*) AS losing_trades FROM trades WHERE net_gain_loss < 0";
$losingTradesResult = $conn->query($losingTradesQuery);
$losingTradesData = $losingTradesResult->fetch_assoc();

$losingTrades = $losingTradesData['losing_trades'];

// Function to calculate time ago
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $currentTime = time();
    $timeDifference = $currentTime - $timestamp;

    $units = [
        31556926 => 'year',
        2629743  => 'month',
        604800   => 'week',
        86400    => 'day',
        3600     => 'hour',
        60       => 'minute',
        1        => 'second'
    ];

    foreach ($units as $unitSeconds => $unitName) {
        if ($timeDifference >= $unitSeconds) {
            $count = floor($timeDifference / $unitSeconds);
            return "$count $unitName" . ($count > 1 ? 's' : '') . " ago";
        }
    }

    return 'Just now';
}

$timeAgo = timeAgo($updatedTime);

// Retrieve weekly profit data
$weeklyProfitQuery = "
    SELECT 
        YEARWEEK(open_date_time, 1) AS week, 
        SUM(net_gain_loss) AS weekly_profit 
    FROM trades 
    GROUP BY week
    ORDER BY week ASC
";
$weeklyProfitResult = $conn->query($weeklyProfitQuery);

$weeks = [];
$weeklyProfits = [];

// Function to get the formatted week label
function getWeekLabel($yearweek) {
    $year = substr($yearweek, 0, 4);
    $week = substr($yearweek, 4);
    $datetime = new DateTime();
    $datetime->setISODate($year, $week);
    $month = $datetime->format('F');
    $weekNumber = $week % 4 + 1;
    return "Week $weekNumber of $month";
}

while ($row = $weeklyProfitResult->fetch_assoc()) {
    $weeks[] = getWeekLabel($row['week']);
    $weeklyProfits[] = $row['weekly_profit'];
}

$recentTradesQuery = "
    SELECT net_gain_loss
    FROM trades
    ORDER BY open_date_time DESC
";
$recentTradesResult = $conn->query($recentTradesQuery);

$recentOutcomes = [];
while ($row = $recentTradesResult->fetch_assoc()) {
    // Determine if the trade was a win, loss, or neutral
    if ($row['net_gain_loss'] > 0) {
        $recentOutcomes[] = 'W';
    } elseif ($row['net_gain_loss'] < 0) {
        $recentOutcomes[] = 'L';
    } else {
        $recentOutcomes[] = 'N';
    }
}

// Reverse the array so that the most recent outcomes appear first
$recentOutcomes = array_reverse($recentOutcomes);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/0d118bca32.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Trading Journal</title>
    <style>
        @font-face {
            font-family: 'CustomFont';
            src: url('Liber v2/LiberGrotesqueFamily-Regular.ttf') format('woff2'),
                 url('Liber v2/LiberGrotesqueFamily-Regular.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .header {
            background-image: url('uploads/cover4.jpg');
            background-size: cover;
            background-position: center 95%;
            height: 300px;
            position: relative;
        }
        .header-content {
            font-family: 'CustomFont', sans-serif;
            position: absolute;
            top: 20%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #fff;
            letter-spacing: 5px;
        }
        .header-content h1 {
            margin: 0;
            font-size: 3rem;
        }
        .container {
            max-width: 100%;
            margin: 20px;
            background-color: #fff;
            padding: 20px 50px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        .add-button {
            text-align: center;
            margin-top: 15px;
        }
        .add-button a {
            text-decoration: none;
        }
        .add-button button {
            background-color: #006D6D;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
            font-size: 16px;
        }
        .add-button button:hover {
            background-color: #004D4D;
        }

        .trades-header {
            width: 100%;
            overflow: auto;
        }
        
        .cards {
            display: flex;
            gap: 20px;
            justify-content: space-between;
            margin-top: 20px;
            margin-right:0;
        }
        .card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            flex: 1;
            transition: transform 0.3s, box-shadow 0.3s;
            width: 40%;
            border-radius: 5px;
            margin: 10px;

        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        .card h3 {
            margin-top: 0;
            color: #333;
        }
        .card p {
            color: #666;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
        }

        .body {
            font-size: 35px;
            margin: 10px 0;
        }

        .footer {
            font-size: 16px;
            color: grey;
            margin-top: 15px;
        }
.cards2 {
            display: flex;
            gap: 20px;
            justify-content: space-between;
            margin-top: 20px;
            margin-right:0;
        }
        .card2 {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            flex: 1;
            transition: transform 0.3s, box-shadow 0.3s;
            width: 60%;
            border-radius: 5px;
            margin: 10px;
            height: 400px;

        }
        .card2:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }

        .chart-container > .card {
            width: 100%; /* Make the card occupy all the horizontal space */
            box-sizing: border-box; /* Ensure padding and border are included in the width */
            margin-right:100px;

        }
        .card-inside-card {
            background-color: #EFEFEF; /* Set the background color for the whole card */
            border-radius: 10px;
            padding: 10px 50px;
            transition: transform 0.3s, box-shadow 0.3s;
            width: 100%;
            border-radius: 5px; /* Note: border-radius is duplicated, you can remove one */
            box-sizing: border-box;
            text-align: center;
            font-size: 20px;
            height:auto;
            margin:10px;

        }
        .card-inside-card p {
            padding: 10px 10px;
            margin: 0 -10px; /* Adjust if necessary to fit your design */
            box-sizing: border-box;
        }
        .left{
        float:left;
        }
        .right{
        float:right;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
        .cards3{
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            margin-right:0;
        }

        .cards3-contents {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            flex: 1;
            transition: transform 0.3s, box-shadow 0.3s;
            width: 90%;
            border-radius: 5px;
            margin: 10px;
            height: 200px;

        }

        .cards3-contents:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        .summary-flex {
            display: flex; /* This makes it a flex container */
            justify-content: space-around; /* This will distribute space around the items */
            align-items: center; /* This will vertically center align the items */
        }
        .summary-icons{
            padding: 5px 20px;
            align-items: center;
            display: flex; /* This makes it a flex container */
            justify-content: center; /* This centers the children horizontally */
            align-items: center; /* This centers the children vertically */
            flex-wrap: wrap; 
        }
        .card-icons {
            display: inline-block;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border-radius: 5px;
            background-color: #006D6D;
            color: #fff;
            margin-right: 10px;
            padding:5px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            padding: 0;
        }
        .modal-content {
            background-color: #fff;
            margin: auto;
            border-radius: 10px;
            width: 300px;
            text-align: center;
            border: 1px solid #ddd;
            padding: 20px;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }
        .contents {
            font-size: 18px;
            text-align: center;
            color: #28a745;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>Trading Journal</h1>
            <p>PATIENCE  •  DISCIPLINE  •  FEARLESS  •  EXECUTION</p>
        </div>
    </div>

    <div class="container">
        <div class="trades-header">
            <div class="add-button" style="float:right; letter-spacing:5px;">
                <a href="index.php" target="_blank"><button type="button"><i class="fa-solid fa-square-up-right"></i> View My Trades</button></a>
            <a href="#" onclick="openCenteredPopup('update.php', 'Add New Trade', 800, 600); return false;">
                <button type="button"><i class="fa-solid fa-pen-to-square"></i> Update Assets</button>
            </a>            
            </div>
            <div class="recent-trades" style="float:left; letter-spacing:2px;">
                <h2>Dashboard</h2>
            </div>
        </div>
        <?php if (isset($_GET['add']) && $_GET['add'] == 'success'): ?>
            <div id="addSuccessModal" class="modal" style="display: block;">
                <div class="modal-content">
                    <p class="contents">Assets updated successfully!</p>
                </div>
            </div>
        <?php endif; ?>
        <div class="cards">
            <div class="card">
                <p class="title">Total Amount</p>
                <p class="body"><strong>$<?php echo number_format($totalAmount, 2); ?></strong></p>  
                <p class="footer">Last updated: <?php echo $timeAgo; ?></p>
            </div>

            <div class="card">
                <p class="title">Average RRR</p>
                <p class="body"><strong><?php echo number_format($averageRRR, 2); ?></strong></p>
                <p class="footer"><?php echo $rrrMessage; ?></p>
            </div>

            <?php 
            // Calculate percentage of profit based on total amount already defined
            $percentageOfProfit = ($totalAmount != 0) ? ($totalProfit / $totalAmount) * 100 : 0;
            $formattedPercentage = number_format($percentageOfProfit, 2);
            ?>
            <div class="card">
                <p class="title">Total Profit</p>
                <p class="body">
                    <strong style="color: <?php echo $totalProfit > 0 ? '#19692c' : ($totalProfit < 0 ? '#a71d2a' : 'inherit'); ?>;">
                        $<?php echo number_format($totalProfit, 2); ?>
                    </strong>
                </p> 
                <p class="footer">
                    <strong style="color: <?php echo $percentageOfProfit > 0 ? '#19692c' : ($percentageOfProfit < 0 ? '#a71d2a' : 'inherit'); ?>;">
                        <?php echo $formattedPercentage; ?>%
                        <?php if ($percentageOfProfit > 0): ?>
                            <i class="fa-solid fa-arrow-up"></i>
                        <?php elseif ($percentageOfProfit < 0): ?>
                            <i class="fa-solid fa-arrow-down"></i>
                        <?php endif; ?>
                    </strong>
                </p>
            </div>
        </div>
         <div class="cards2">
                <div class="card2">
                <canvas id="myChart"></canvas>
                </div>
                <div class="card" style="padding:10px 50px;  ">
                <p class="title">Recent Activities</p>
                <div class="overlapz" style="overflow-x: hidden; overflow-y: auto; max-height: 300px;">
                    <?php
                    $recentActivitiesQuery = "SELECT * FROM recent_activity ORDER BY activityID DESC";
                    $recentActivitiesResult = $conn->query($recentActivitiesQuery);
                    if ($recentActivitiesResult->num_rows > 0) {
                        while ($row = $recentActivitiesResult->fetch_assoc()) {
                            echo '<div class="card-inside-card clearfix">
                                    <p class="left">' . date("F j, Y, g:i a", strtotime($row['date_time'])) . ' - <strong>' . $row['type'] . '</strong> </p>
                                    <p class="right">Amount: $'.number_format($row['amount'], 2).'</p> <br> </div>';
                        }
                    } else {
                        echo '<div class="card-inside-card clearfix">No recent activities</div>';
                    }
                    ?>
                </div>
                    
                    
               
            </div>
            </div>

        <div class="cards3">
            <div class="cards3-contents">
            <h2 style="color:#666">Trade Summary</h2>
            <div class="summary-flex">
                    <p style="color:#666;">Total Trades: <?php echo $totalTrades; ?></p>
                    <p style="color:green;">Winning Trades: <?php echo $winningTrades; ?></p>
                    <p style="color:#79305a;">Losing Trades: <?php echo $losingTrades; ?></p>
            </div>
               <div class="summary-icons">
                    <?php
                    // Example variables $recentOutcomes and $recentOutcomesCount assumed to be available
                    if (!empty($recentOutcomes)) {
                        foreach ($recentOutcomes as $outcome) {
                            echo '<div class="card-icons">' . htmlspecialchars($outcome) . '</div>';
                        }
                    } else {
                        echo '<p>No recent outcomes</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
<script>
function openCenteredPopup(url, title) {
    const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
    const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screenY;

    const windowWidth = 500; // Fixed width of 500px
    const windowHeight = 500;

    const left = ((screen.width / 2) - (windowWidth / 2)) + dualScreenLeft;
    const top = 0 + dualScreenTop;

    const newWindow = window.open(url, title, `scrollbars=yes, width=${windowWidth}, height=${windowHeight}, top=${top}, left=${left}`);

    if (window.focus) {
        newWindow.focus();
    }
}
</script>
    <script>
        // Weekly profit data from PHP
        const weeks = <?php echo json_encode($weeks); ?>;
        const weeklyProfits = <?php echo json_encode($weeklyProfits); ?>;

const config = {
    type: 'bar',
    data: {
        labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'], // Directly set the labels here
        datasets: [{
            label: 'Weekly Profit',
            backgroundColor: '#006D6D',
            borderColor: '#006D6D',
            borderRadius: 20, // Set border radius for all bars
            data: weeklyProfits, // Ensure this array has values corresponding to each week
        }]
    },
    options: {
        indexAxis: 'x', // Horizontal bars
        scales: {
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Profit'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Weeks of the Month'
                }
                // Removed the ticks.callback function for simplicity
            }
        },
        plugins: {
            title: {
                display: true,
                text: 'Weekly Profit'
            }
        }
    },
};

// Create the chart
var myChart = new Chart(
    document.getElementById('myChart'),
    config
);
    </script>
<script>
        // Close the modal after 2 seconds
        setTimeout(function() {
            var modal = document.getElementById("addSuccessModal");
            if (modal) {
                modal.style.display = "none";
            }
        }, 2000);
</script>

</body>
</html>

<?php
$conn->close();
?>
