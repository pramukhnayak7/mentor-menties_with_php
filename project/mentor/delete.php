<?php
require 'db.php';

if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);
    $stmt = $conn->prepare("DELETE FROM notifications WHERE sender_id = ? OR receiver_id = ?");
    $stmt->bind_param("ii", $userId, $userId);
    $stmt->execute();


    $stmt = $conn->prepare("DELETE FROM academic_records WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        echo "User and all related data deleted successfully.";
        header("Location: user-list.php");
        exit();
    } else {
        echo "Error deleting user.";
    }
} else {
    echo "Invalid request.";
}
?>
