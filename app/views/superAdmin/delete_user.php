<?php
session_start();
include('../../../config/config.php');

$id = $_GET['id'];

$deleteQuery = "DELETE FROM available_leaves WHERE user_id = ?";
$stmt = $conn->prepare($deleteQuery);

if (!$stmt) {
    // If statement preparation fails
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $deleteQuery = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);

    if (!$stmt) {
        // If statement preparation fails for users table
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: view_users.php"); // Redirect to the page where the table is displayed
        exit();
    } else {
        // Output error details if the execution fails
        die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
    }
} else {
    // Output error details if the execution fails
    die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
}

$conn->close();
?>
