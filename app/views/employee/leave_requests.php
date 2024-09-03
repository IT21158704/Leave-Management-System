
<?php

include('../../../config/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Employee') {
    header("Location: ../logout.php");
    exit();
}

$id = $_SESSION['user_id'];

$conn->close();

?>

<head>
    <meta charset="UTF-8">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> <!-- Select2 CSS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> <!-- Select2 JS -->
</head>

<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Employee Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="employee_dashboard.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="leave_application.php">Leave Application</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="leave_requests.php">Leave Requests</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>
  
    <div class="container mt-4">
        <header>
            <h3 class="mb-4">
                Requests Of Leave    
            <!-- Welcome, <?php echo htmlspecialchars($username); ?>! -->
            </h3>
        </header>

        <div class="mb-3">
            <input class="form-control" id="searchInput" type="text" placeholder="Search...">
        </div>

        <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Requests of Leave</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        button {
            padding: 8px 12px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin: 0 5px;
        }
        .view-btn {
            background-color: #17a2b8;
            color: white;
        }
        .accept-btn {
            background-color: #28a745;
            color: white;
        }
        .reject-btn {
            background-color: #dc3545;
            color: white;
        }
        button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

    <h1>Requests of Leave</h1>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Employee Name</th>
                <th>Leave Type</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>John Doe</td>
                <td>Annual Leave</td>
                <td>2024-09-05</td>
                <td>2024-09-10</td>
                <td>
                    <button class="view-btn">View Request</button>
                    <button class="accept-btn">Accept</button>
                    <button class="reject-btn">Reject</button>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>Jane Smith</td>
                <td>Sick Leave</td>
                <td>2024-09-07</td>
                <td>2024-09-09</td>
                <td>
                    <button class="view-btn">View Request</button>
                    <button class="accept-btn">Accept</button>
                    <button class="reject-btn">Reject</button>
                </td>
            </tr>
            <!-- Add more rows as needed -->
        </tbody>
    </table>

</body>
</html>
