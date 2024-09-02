<?php
include('../../config/config.php');
session_start();

$error_message = ''; // Initialize error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check database connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, password, role FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password, $role);

    if ($stmt->num_rows == 1) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            setcookie("username", $username, time() + (86400 * 30), "/"); // 30 days

            // Redirect based on user role
            switch ($role) {
                case 'Admin':
                    header("Location: ./admin/admin_dashboard.php");
                    break;
                case 'Employee':
                    header("Location: ./employee/employee_dashboard.php");
                    break;
                case 'Supervising Officer':
                    header("Location: supervising_officer_dashboard.php");
                    break;
                case 'Head of Department':
                    header("Location: head_of_department_dashboard.php");
                    break;
                case 'Officer Acting':
                    header("Location: officer_acting_dashboard.php");
                    break;
                default:
                    $error_message = 'Role not recognized.';
                    break;
            }
            exit();
        } else {
            $error_message = 'Invalid password';
        }
    } else {
        $error_message = 'No user found with that username';
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('background_image.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: white;
            opacity: 0.95;
        }

        .login-header {
            background-color: #004085;
            color: white;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }

        .login-body {
            padding: 20px;
        }

        .login-body .form-control {
            margin-bottom: 15px;
        }

        .login-footer {
            padding: 10px;
            text-align: center;
        }

        .logo {
            display: block;
            margin: 0 auto 20px;
            width: 80px;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <div class="login-header">
            <img src="../assets/images/logo.png" alt="Government Logo" class="logo">
            <h3>Leave Management System</h3>
            <h5>Ministry of Fisheries</h5>
        </div>
        <div class="login-body">
            <form method="post" action="">
                <?php if (!empty($error_message)): ?>
                    <div class="login-footer">
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error_message; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>
</body>

</html>