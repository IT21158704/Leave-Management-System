<?php
session_start();
include('../../../config/config.php');

$id = $_GET['id'];
$error_message = '';

$deleteQuery = "DELETE FROM available_leaves WHERE user_id = ?";
$stmt = $conn->prepare($deleteQuery);

if (!$stmt) {
    // If statement preparation fails
    $error_message = "Error deleting from available_leaves.";
    header("Location: view_users.php?status=error&message=" . urlencode($error_message));
    exit();
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $deleteQuery = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);

    if (!$stmt) {
        // If statement preparation fails for users table
        $error_message = "Error deleting from users.";
        header("Location: view_users.php?status=error&message=" . urlencode($error_message));
        exit();
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // If delete is successful
        header("Location: view_users.php?status=success&message=" . urlencode("User successfully deleted."));
        exit();
    } else {
        // Error during execution - Handle specific errors
        if ($stmt->errno == 1451) { // Foreign key constraint error
            $error_message = "Unable to delete user. This user is associated with other records (e.g., leave applications). Please update or remove those associations first.";
        } else {
            $error_message = "An error occurred while trying to delete the user.";
        }
        header("Location: view_users.php?status=error&message=" . urlencode($error_message));
        exit();
    }
} else {
    // Error during execution
    $error_message = "An error occurred while trying to delete from available_leaves.";
    header("Location: view_users.php?status=error&message=" . urlencode($error_message));
    exit();
}

$conn->close();
?>
