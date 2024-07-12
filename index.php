<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "trading_journal";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve data with limit and offset for pagination
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$sql = "SELECT * FROM trades ORDER BY open_date_time DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/0d118bca32.js" crossorigin="anonymous"></script>
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
            font-family:'CustomFont', sans-serif;
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
            padding: 20px;
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
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            color: #006D6D;
            border: 1px solid #006D6D;
            border-radius: 5px;
            margin: 0 4px;
        }
        .pagination a.active, .pagination a:hover {
            background-color: #006D6D;
            color: white;
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

        .modal-content button {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
            font-size: 16px;
        }

        .modal-content button#confirmDeleteBtn {
            background-color: #006D6D;
            color: white;
        }

        .modal-content button#confirmDeleteBtn:hover {
            background-color: #004D4D;
        }

        .modal-content button#cancelDeleteBtn {
            background-color: gray;
            color: white;
        }

        .modal-content button#cancelDeleteBtn:hover {
            background-color: #333333;
        }
        .contents {
            font-size: 18px;
            text-align: center;
            color: #28a745;
            font-weight: bold;
        }
        .action-buttons a {
            display: inline-block;
            padding: 10px 12px;
            text-decoration: none;
            color: #fff;
            background-color: #006D6D;
            border-radius: 5px;
            margin-right: 5px;
        }
        .action-buttons a:hover {
            background-color: #004D4D;
        }
        .action-buttons a.delete {
            background-color: #dc3545;
        }
        .action-buttons a.delete:hover {
            background-color: #c82333;
        }
        .trades-header {
            width: 100%;
            overflow: auto;
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
            <div class="add-button">
            <a href="#" onclick="openCenteredPopup('add_trade.html', 'Add New Trade', 800, 600); return false;">
                <button type="button"><i class="fa-solid fa-plus"></i> Add New Trade</button>
            </a>     
            <a href="dashboard.php" target="_blank"><button type="button"><i class="fa-solid fa-chart-column"></i> Open Analytics</button></a>
            </div>
            <div class="recent-trades">
                <h2>Recent Trades</h2>
            </div>
        </div>

        <?php if (isset($_GET['add']) && $_GET['add'] == 'success'): ?>
            <div id="addSuccessModal" class="modal" style="display: block;">
                <div class="modal-content">
                    <p class="contents">Trade added successfully!</p>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['delete']) && $_GET['delete'] == 'success'): ?>
            <div id="deleteSuccessModal" class="modal" style="display: block;">
                <div class="modal-content">
                    <p class="contents">Trade deleted successfully!</p>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['update']) && $_GET['update'] == 'success'): ?>
            <div id="updateSuccessModal" class="modal" style="display: block;">
                <div class="modal-content">
                    <p class="contents">Trade updated successfully!</p>
                </div>
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>Open Date & Time</th>
                    <th>Symbol</th>
                    <th>Direction</th>
                    <th>Margin</th>
                    <th>Leverage</th>
                    <th>Net Gain/Loss</th>
                    <th>%</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
<?php
                if ($result->num_rows > 0) {
                    $today = new DateTime(); // Today's date
                    while($row = $result->fetch_assoc()) {
                    $percentage = ($row["net_gain_loss"] / $row["margin"]) * 100;


                        // Parse the open_date_time from the database
                        $openDateTime = new DateTime($row["open_date_time"]);
                        $formattedDate = $openDateTime->format('Y-m-d') === $today->format('Y-m-d') 
                                        ? "Today" 
                                        : $openDateTime->format('M d, Y');
                        $formattedTime = $openDateTime->format('h:i A');

                        echo "<td>" . $formattedDate . ", " . $formattedTime . "</td>";
                        echo "<td>" . $row["symbol"] . "</td>";

                        if ($row["direction"] === "Long") {
                            echo "<td style='color: #28a745;'>" . $row["direction"] . "</td>"; // Green shade
                        } elseif ($row["direction"] === "Short") {
                            echo "<td style='color: #dc3545;'>" . $row["direction"] . "</td>"; // Red shade
                        } else {
                            echo "<td>" . $row["direction"] . "</td>"; // Default color if neither Long nor Short
                        }

                        echo "<td>" . $row["margin"] . "</td>";
                        echo "<td>" . $row["leverage"] . "</td>";
                        $netGainLossColor = '#000000'; // Default to black
                        if ($row["net_gain_loss"] < 0) {
                            $netGainLossColor = '#a71d2a'; // Darker red if below 0
                        } elseif ($row["net_gain_loss"] > 0) {
                            $netGainLossColor = '#19692c'; // Darker green if above 0
                        }
                        echo "<td style='color: " . $netGainLossColor . ";'><strong>" . $row["net_gain_loss"] . "</strong></td>";

                        $percentageColor = '#000000'; // Default to black
                        if ($percentage < 0) {
                            $percentageColor = '#a71d2a'; // Darker red if below 0
                        } elseif ($percentage > 0) {
                            $percentageColor = '#19692c'; // Darker green if above 0
                        }
                        echo "<td style='color: " . $percentageColor . ";'><strong>" . number_format($percentage, 2) . "%</strong></td>";
                        echo "<td class='action-buttons'>";
                        echo "<a href='view_trade.php?id=" . $row["id"] . "' target='_blank'><i class='fa-solid fa-arrow-up-right-from-square'></i> View</a>";                        
                        echo "<a href='edit_trade.php?id=" . $row["id"] . "' onclick='openUpdateWindow(event, this)' data-id='" . $row["id"] . "'><i class='fa-solid fa-pencil'></i> Update</a>";
                        echo "<a href='#' class='delete' data-trade-id='" . $row["id"] . "'><i class='fa-solid fa-trash'></i> Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No trades found</td></tr>";
                }
                ?>      
            </tbody>
        </table>

        <div class="pagination">
            <?php
            $sql = "SELECT COUNT(*) AS total FROM trades";
            $result = $conn->query($sql);
            $totalRows = $result->fetch_assoc()['total'];
            $totalPages = ceil($totalRows / $limit);

            for ($i = 1; $i <= $totalPages; $i++) {
                echo "<a href='index.php?page=$i' class='" . ($i == $page ? 'active' : '') . "'>$i</a>";
            }
            ?>
        </div>
    </div>
    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmationModal" class="modal">
        <div class="modal-content">
            <p>Are you sure you want to delete this trade?</p>
            <button id="confirmDeleteBtn">Confirm</button>
            <button id="cancelDeleteBtn">Cancel</button>
        </div>
    </div>

    <!-- Modal for Successful Deletion -->
    <div id="deleteSuccessModal" class="modal">
        <div class="modal-content">
            <p class="contents">Trade deleted successfully.</p>
        </div>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const deleteLinks = document.querySelectorAll('.action-buttons a.delete');
        const deleteConfirmationModal = document.getElementById('deleteConfirmationModal');
        const deleteSuccessModal = document.getElementById('deleteSuccessModal');
        let tradeIdToDelete = null;

        // Open delete confirmation modal
        deleteLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                tradeIdToDelete = this.getAttribute('data-trade-id');
                deleteConfirmationModal.style.display = 'block';
            });
        });

        // Cancel delete action
        const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
        cancelDeleteBtn.addEventListener('click', function() {
            deleteConfirmationModal.style.display = 'none';
            tradeIdToDelete = null; // Reset trade id
        });

        // Confirm delete action
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        confirmDeleteBtn.addEventListener('click', function() {
            // Perform delete action here (redirect or AJAX call)
            if (tradeIdToDelete) {
                // Example: Redirect to delete script with trade id
                window.location.href = 'delete_trade.php?id=' + tradeIdToDelete;
                // Alternatively, you can use AJAX to delete without redirecting
            }
            deleteConfirmationModal.style.display = 'none';
        });

        // Code for handling success modal (if needed)
        const urlParams = new URLSearchParams(window.location.search);
        const deleteStatus = urlParams.get('delete');
        if (deleteStatus === 'success') {
            deleteSuccessModal.style.display = 'block';
            setTimeout(function() {
                deleteSuccessModal.style.display = 'none';
                history.pushState(null, '', location.href.split('?')[0]);
            }, 2000);
        }
    });
    
    </script>
    <script>
        // Close the modal after 2 seconds
        setTimeout(function() {
            var modal = document.getElementById("addSuccessModal");
            if (modal) {
                modal.style.display = "none";
            }
            var deleteModal = document.getElementById("deleteSuccessModal");
            if (deleteModal) {
                deleteModal.style.display = "none";
            }
            var updateModal = document.getElementById("updateSuccessModal");
            if (updateModal) {
                updateModal.style.display = "none";
            }
        }, 2000);



        // Open centered popup function
function openCenteredPopup(url, title) {
    const dualScreenLeft = window.screenLeft !== undefined ? window.screenLeft : window.screenX;
    const dualScreenTop = window.screenTop !== undefined ? window.screenTop : window.screenY;

    const windowWidth = 500; // Fixed width of 500px
    const windowHeight = window.innerHeight || document.documentElement.clientHeight || screen.height;

    const left = ((screen.width / 2) - (windowWidth / 2)) + dualScreenLeft;
    const top = 0 + dualScreenTop;

    const newWindow = window.open(url, title, `scrollbars=yes, width=${windowWidth}, height=${windowHeight}, top=${top}, left=${left}`);

    if (window.focus) {
        newWindow.focus();
    }
}
    </script>
    <script>
        function openUpdateWindow(event, element) {
            event.preventDefault();
            const id = element.getAttribute('data-id');
            const url = 'edit_trade.php?id=' + id;
            const width = 800;
            const height = 600;
            const left = (window.screen.width / 2) - (width / 2);
            const top = (window.screen.height / 2) - (height / 2);
            const features = `scrollbars=yes, width=${width}, height=${height}, top=${top}, left=${left}`;

            // Open the update trade window
            window.open(url, '_blank', features);

            // Optional: Close the success modal after opening the update window
            const updateSuccessModal = document.getElementById('updateSuccessModal');
            if (updateSuccessModal) {
                updateSuccessModal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
