<?php
include('../../../config/config.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin') {
    header("Location: ../login.php");
    exit();
}

$id = $_GET['id'];

// Fetch existing data
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    die("Record not found");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $designation = $_POST['designation'];
    $dept = $_POST['dept'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    $updateQuery = "UPDATE users SET name = ?, designation = ?, dept = ?, username = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("sssssi", $name, $designation, $dept, $username, $role, $id);

    if ($stmt->execute()) {
        header("Location: view_users.php"); // Redirect to the dashboard or another page
        exit();
    } else {
        echo '<div class="alert alert-danger" role="alert">Update failed: ' . $stmt->error . '</div>';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 600px;
            margin-top: 50px;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 30px;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">Home</a>
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
            <h1 class="mb-4">Update Record</h1>
        </header>

        <!-- Update Form -->
        <form method="post" class="needs-validation" novalidate>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                <div class="invalid-feedback">Please enter the name.</div>
            </div>
            <div class="form-group">
                <label for="designation">Designation</label>
                <input type="text" class="form-control" id="designation" name="designation" value="<?php echo htmlspecialchars($row['designation']); ?>" required>
                <div class="invalid-feedback">Please enter the designation.</div>
            </div>
            <div class="form-group">
                <label for="dept">Ministry/Dept.</label>
                <input type="text" class="form-control" id="dept" name="dept" value="<?php echo htmlspecialchars($row['dept']); ?>" required>
                <div class="invalid-feedback">Please enter the ministry or department.</div>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" required>
                <div class="invalid-feedback">Please enter a username.</div>
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select class="form-control" id="role" name="role" required>
                    <option value="Employee" <?php if ($row['role'] == 'Employee') echo 'selected'; ?>>Employee</option>
                    <option value="Supervising Officer" <?php if ($row['role'] == 'Supervising Officer') echo 'selected'; ?>>Supervising Officer</option>
                    <option value="Head of Department" <?php if ($row['role'] == 'Head of Department') echo 'selected'; ?>>Head of Department</option>
                    <option value="Officer Acting" <?php if ($row['role'] == 'Officer Acting') echo 'selected'; ?>>Officer Acting</option>
                    <option value="Admin" <?php if ($row['role'] == 'Admin') echo 'selected'; ?>>Admin</option>
                </select>
                <div class="invalid-feedback">Please select a role.</div>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
            <a href="view_users.php" class="btn btn-secondary">Back to list</a>
            <a href="password_reset.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-success">Reset Password</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
    <script>
        // Bootstrap form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>

</html>