<?php
require('../../assets/vendors/fpdf/fpdf.php');
include('../../../config/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Subject Officer') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_GET['id'];
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo htmlspecialchars($_SESSION['role']); ?></title>
    <link rel="stylesheet" href="../../assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="../../assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../../assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../../assets/vendors/datatables.net-bs5/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="shortcut icon" href="../../assets/images/favicon.svg" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
            <a class="navbar-brand brand-logo me-5" href="../../../public/index.php"><img src="../../assets/images/logo.svg" class="me-2" alt="logo" /></a>
            <a class="navbar-brand brand-logo-mini" href="../../../public/index.php"><img src="../../assets/images/logo-mini.svg" alt="logo" /></a>
        </div>
    </nav>

    <div class="container-fluid page-body-wrapper">
        <nav class="sidebar sidebar-offcanvas" id="sidebar">
            <ul class="nav">
                <li class="nav-item"><a class="nav-link" href="subject_officer_dashboard.php"><i class="icon-grid menu-icon"></i><span class="menu-title">Home</span></a></li>
                <li class="nav-item"><a class="nav-link" href="users.php"><i class="mdi mdi-account-multiple-outline menu-icon"></i><span class="menu-title">Users</span></a></li>
                <li class="nav-item"><a class="nav-link" href="shortLeavs.php"><i class="mdi mdi-timelapse menu-icon"></i><span class="menu-title">Short Leaves</span></a></li>
                <li class="nav-item"><a class="nav-link" href="leave_application.php"><i class="mdi mdi-note-plus-outline menu-icon"></i><span class="menu-title">Leave Application</span></a></li>
                <li class="nav-item"><a class="nav-link" href="leave_application_history.php"><i class="mdi mdi-history menu-icon"></i><span class="menu-title">Leave History</span></a></li>
                <li class="nav-item"><a class="nav-link" href="leave_requests.php"><i class="mdi mdi-bookmark-outline menu-icon"></i><span class="menu-title">Leave Requests</span></a></li>
                <li class="nav-item"><a class="nav-link" href="emergencyLeaves.php"><i class="mdi mdi-alert-octagon-outline menu-icon"></i><span class="menu-title">Emergency Leave</span></a></li>
                <li class="nav-item"><a class="nav-link" href="profile.php"><i class="icon-head menu-icon"></i><span class="menu-title">Profile</span></a></li>
                <li class="nav-item"><a class="nav-link" href="../logout.php"><i class="mdi mdi-logout menu-icon"></i><span class="menu-title">Logout</span></a></li>
            </ul>
        </nav>

        <div class="main-panel">
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-md-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="card-title">Short Leave History</p>
                                    <div class="d-flex">
                                        <div class="mb-3 mr-2">
                                            <select class="btn btn-outline" id="yearPicker" onchange="filterTable()">
                                                <option value="">Select Year</option>
                                                <?php
                                                // Fetch and display years (modify as per your requirements)
                                                for ($year = date("Y"); $year >= 2000; $year--) {
                                                    echo "<option value='$year'>$year</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="mb-3 mr-2">
                                            <select class="btn btn-outline" id="monthPicker" onchange="filterTable()">
                                                <option value="">Select Month</option>
                                                <?php
                                                // Array of month names
                                                $months = [
                                                    "January",
                                                    "February",
                                                    "March",
                                                    "April",
                                                    "May",
                                                    "June",
                                                    "July",
                                                    "August",
                                                    "September",
                                                    "October",
                                                    "November",
                                                    "December"
                                                ];

                                                // Loop through the array to create options
                                                foreach ($months as $index => $month) {
                                                    // $index + 1 gives the month number (1 to 12)
                                                    echo "<option value='" . ($index + 1) . "'>$month</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex flex-wrap">
                                    <div class="col-md-12">
                                        <?php
                                        // Fetch data from the database for a given user_id
                                        $sql = "SELECT id, short_leaves, timestamp FROM short_leave_history WHERE user_id = $user_id ORDER BY timestamp DESC";
                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            // Display the data in a table
                                            echo "<table id='leaveTable'>
                                                    <thead>
                                                        <tr>
                                                            <th>Id</th>
                                                            <th>Short Leaves</th>
                                                            <th>Update Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>";

                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>
                                                        <td>" . $row['id'] . "</td>
                                                        <td>" . $row['short_leaves'] . "</td>
                                                        <td>" . $row['timestamp'] . "</td>
                                                      </tr>";
                                            }

                                            echo "</tbody></table>";
                                        } else {
                                            echo "<p>No leave history found for this user.</p>";
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript function to filter table by month and year
        function filterTable() {
            var selectedYear = document.getElementById('yearPicker').value;
            var selectedMonth = document.getElementById('monthPicker').value;
            var table = document.getElementById('leaveTable');
            var rows = table.getElementsByTagName('tr');

            for (var i = 1; i < rows.length; i++) { // Start from 1 to skip the table header
                var dateCell = rows[i].getElementsByTagName('td')[2]; // Date is in the third cell
                if (dateCell) {
                    var date = new Date(dateCell.textContent || dateCell.innerText);
                    var year = date.getFullYear();
                    var month = date.getMonth() + 1; // Months are 0-based

                    if ((selectedYear === "" || year == selectedYear) && (selectedMonth === "" || month == selectedMonth)) {
                        rows[i].style.display = ""; // Show row
                    } else {
                        rows[i].style.display = "none"; // Hide row
                    }
                }
            }
        }
    </script>

</body>
