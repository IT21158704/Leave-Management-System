<?php

include('../../../config/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</head>

<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="admin_dashboard.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_users.php">View Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_user.php">Add User</a>
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
                Registered Users    
            <!-- Welcome, <?php echo htmlspecialchars($username); ?>! -->
            </h3>
        </header>

        <div class="mb-3">
            <input class="form-control" id="searchInput" type="text" placeholder="Search...">
        </div>

        <?php
        // Fetch data from database
        $query = "SELECT * FROM users";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {
            echo '<div class="table-responsive">';
            echo '<table class="table table-striped table-hover table-bordered" id="userTable">';
            echo '<thead class="thead-dark">
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>
                        <th scope="col">Designation</th>
                        <th scope="col">Ministry / Dept</th>
                        <th scope="col">Username</th>
                        <th scope="col">Role</th>
                        <th scope="col">Action</th>
                    </tr>
                  </thead>
                  <tbody>';

            while ($row = $result->fetch_assoc()) {
                echo '<tr>
                        <td>' . htmlspecialchars($row['id']) . '</td>
                        <td>' . htmlspecialchars($row['name']) . '</td>
                        <td>' . htmlspecialchars($row['designation']) . '</td>
                        <td>' . htmlspecialchars($row['dept']) . '</td>
                        <td>' . htmlspecialchars($row['username']) . '</td>
                        <td>' . htmlspecialchars($row['role']) . '</td>
                        <td>
                            <a class="btn btn-primary btn-sm" href="update_user.php?id=' . htmlspecialchars($row['id']) . '">Edit</a> 
                            <a class="btn btn-danger btn-sm" href="#" onclick="confirmDelete(' . htmlspecialchars($row['id']) . ')">Delete</a>
                        </td>
                      </tr>';
            }

            echo '</tbody></table>';
            echo '</div>';
        } else {
            echo '<div class="alert alert-warning" role="alert">No records found.</div>';
        }
        ?>

    </div>

    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            var input = document.getElementById('searchInput').value.toLowerCase();
            var table = document.getElementById('userTable');
            var trs = table.getElementsByTagName('tr');

            for (var i = 1; i < trs.length; i++) {
                var tds = trs[i].getElementsByTagName('td');
                var match = false;

                for (var j = 0; j < tds.length; j++) {
                    if (tds[j].innerText.toLowerCase().indexOf(input) > -1) {
                        match = true;
                        break;
                    }
                }

                trs[i].style.display = match ? '' : 'none';
            }
        });

        function confirmDelete(id) {
            if (confirm("Are you sure you want to delete this record?")) {
                window.location.href = 'delete_user.php?id=' + id;
            }
        }
    </script>

</body>

</html>
