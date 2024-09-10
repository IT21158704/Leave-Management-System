<?php
session_start();
include('../../../config/config.php');

$id = $_GET['id'];

$deleteQuery = "DELETE FROM available_leaves WHERE user_id = ?";
$stmt = $conn->prepare($deleteQuery);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $deleteQuery = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: view_users.php"); // Redirect to the page where the table is displayed
        exit();
    } else {
        echo "Delete failed";
    }
    exit();
} else {
    echo "Delete failed";
}



$conn->close();
