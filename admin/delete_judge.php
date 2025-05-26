<?php
include '../db_connect.php';

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$judge_id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM judges WHERE id = ?");
$stmt->bind_param("i", $judge_id);

if ($stmt->execute()) {
    header("Location: add_judge.php");
    exit();
} else {
    echo "Error deleting judge: " . $stmt->error;
}
