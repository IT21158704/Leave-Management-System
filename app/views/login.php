<?php
include('../../config/config.php');
session_start();

$error_message = ''; // Initialize error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nic = $_POST['nic'];
    $password = $_POST['password'];

    // Check database connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, password, role FROM users WHERE nic = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("s", $nic);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password, $role);

    if ($stmt->num_rows == 1) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['nic'] = $nic;
            $_SESSION['role'] = $role;
            setcookie("nic", $nic, time() + (86400 * 30), "/"); // 30 days

            // Redirect based on user role
            switch ($role) {
                case 'Admin':
                    header("Location: ./admin/admin_dashboard.php");
                    break;
                case 'Employee':
                    header("Location: ./employee/employee_dashboard.php");
                    break;
                case 'Supervising Officer':
                    header("Location: ./supervisingOfficer/supervising_officer_dashboard.php");
                    break;
                case 'Subject Officer':
                    header("Location: ./subjectOfficer/subject_officer_dashboard.php");
                    break;
                case 'Staff Officer':
                    header("Location: ./staffOfficer/staff_officer_dashboard.php");
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
        $error_message = 'No user found with that nic';
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
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="../../assets/vendors/feather/feather.css">
    <link rel="stylesheet" href="../../assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../../assets/vendors/css/vendor.bundle.base.css">
    <link rel="stylesheet" href="../../assets/vendors/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="../../assets/vendors/datatables.net-bs5/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="../../assets/vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" type="text/css" href="../../assets/js/select.dataTables.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="shortcut icon" href="../assets/images/favicon.svg" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('../assets//images/bg.jpg');
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
            <img src="../assets/images/sl-logo.svg" alt="Government Logo" class="logo">
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
                    <label for="nic">NIC</label>
                    <input type="text" class="form-control" id="nic" name="nic" placeholder="Enter NIC Number" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>
</body>

</html>