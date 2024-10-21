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

// $user_id = '38'; // For testing purpose, hardcoded, replace with your logic
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
        table, th, td {
            border: 1px solid black;
        }
        th, td {
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
                <li class="nav-item">
                    <a class="nav-link" href="subject_officer_dashboard.php">
                        <i class="icon-grid menu-icon"></i>
                        <span class="menu-title">Home</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="users.php">
                        <i class="mdi mdi-bookmark-outline menu-icon"></i>
                        <span class="menu-title">Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">
                        <i class="icon-head menu-icon"></i>
                        <span class="menu-title">Profile</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">
                        <i class="mdi mdi-logout menu-icon"></i>
                        <span class="menu-title">Logout</span>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="main-panel">
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-md-12 grid-margin stretch-card">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="card-title">Leave History</p>
                                    <div class="d-flex">
                                        <div class="mb-3 mr-2">
                                            <select class="btn btn-outline" id="yearPicker" onchange="filterTable()">
                                                <option value="">Select Year</option>
                                                <?php
                                                $currentYear = date("Y");
                                                for ($i = $currentYear; $i >= 2020; $i--) {
                                                    echo "<option value='$i'>$i</option>";
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
                                        $sql = "SELECT year, casual_leaves, rest_leaves FROM leave_history WHERE user_id = $user_id";
                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            // Display the data in a table
                                            echo "<table id='leaveTable'>
                                                    <thead>
                                                        <tr>
                                                            <th>Year</th>
                                                            <th>Casual Leaves</th>
                                                            <th>Rest Leaves</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>";

                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>
                                                        <td>" . $row['year'] . "</td>
                                                        <td>" . $row['casual_leaves'] . "</td>
                                                        <td>" . $row['rest_leaves'] . "</td>
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
        // JavaScript function to filter table by year
        function filterTable() {
            var selectedYear = document.getElementById('yearPicker').value;
            var table = document.getElementById('leaveTable');
            var rows = table.getElementsByTagName('tr');

            for (var i = 1; i < rows.length; i++) { // Start from 1 to skip the table header
                var yearCell = rows[i].getElementsByTagName('td')[0]; // Year is in the first cell
                if (yearCell) {
                    var year = yearCell.textContent || yearCell.innerText;
                    if (selectedYear === "" || year === selectedYear) {
                        rows[i].style.display = ""; // Show row
                    } else {
                        rows[i].style.display = "none"; // Hide row
                    }
                }
            }
        }
    </script>

</body>
