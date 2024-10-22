<?php
include('../../../config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dept'])) {
    $dept = $_POST['dept'];
    $query = "SELECT id, name, designation FROM users WHERE role = 'Staff Officer' AND dept IN (?, 'Secretary')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $dept);
    $stmt->execute();
    $result = $stmt->get_result();

    $staffOfficers = [];
    while ($row = $result->fetch_assoc()) {
        $staffOfficers[] = $row;
    }

    echo json_encode($staffOfficers);
}
?>
