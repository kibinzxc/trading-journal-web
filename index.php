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
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        .header {
            background-image: url('uploads/cover4.jpg'); /* Sample placeholder image */
            background-size: cover;
            background-position: center 95%;
            height: 300px; /* Adjust height as needed */
            position: relative;
        }
        .header-content {
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
            padding: 12px 8px; /* Adjusted padding */
            text-align: center; /* Center align text */
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
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            padding: 0;
        }
        .modal-content {
            background-color: #fff; /* Light green for background */
            margin: auto; /* Centered in the viewport */
            border-radius: 10px;
            width: 300px; /* Adjust width as needed */
            text-align: center;
            border: 1px solid #ddd;
            padding: 20px;
            position: absolute; /* Changed to absolute */
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
            color: #28a745; /* Vibrant green for text */
            font-weight: bold;
        }
        .action-buttons a {
            display: inline-block;
            padding: 10px 12px; /* Adjusted padding */
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
            overflow: auto; /* Clear fix for floated child elements */
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
            <a href="" onclick="openCenteredPopup('add_trade.html', 'Add New Trade', 800, 600)">
                <button type="button"><i class="fa-solid fa-plus"></i> Add New Trade</button>
            </a>                
            <a href="add_trade.html" target="_blank"><button type="button"><i class="fa-solid fa-chart-column"></i> Open Analytics</button></a>
            </div>
            <div class="recent-trades">
                <h2>Recent Trades</h2>
            </div>
        </div>
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
                        echo "<tr>";

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
                        echo "<td>" . $row["net_gain_loss"] . "</td>";
                        echo "<td>" . $row["percentage"] . "%</td>";
                        echo "<td class='action-buttons'>";
                        echo "<a href='view_trade.php?id=" . $row["id"] . "' target='_blank'><i class='fa-solid fa-pencil'></i> Edit</a>";
                        echo "<a href='view_trade.php?id=" . $row["id"] . "' target='_blank'><i class='fa-solid fa-arrow-up-right-from-square'></i> View</a>";                        
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

        <!-- Pagination -->
        <?php
        // Count total number of rows
        $sql = "SELECT COUNT(*) AS total FROM trades";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $total_pages = ceil($row["total"] / $limit);

        // Render pagination links
        echo "<div class='pagination'>";
        for ($i = 1; $i <= $total_pages; $i++) {
            $active_class = $i == $page ? 'active' : '';
            echo "<a href='?page=$i' class='$active_class'>$i</a>";
        }
        echo "</div>";
        ?>
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
</body>
</html>

<?php
$conn->close();
?>
<script>
function openCenteredPopup(url, title, width, height) {
    const screenHeight = screen.height;
    height = screenHeight; 
    const left = (screen.width - width) / 2;
    const top = 0;
    const options = `toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=${width}, height=${height}, top=${top}, left=${left}`;
    window.open(url, title, options);
}
</script>