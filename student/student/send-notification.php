<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_SESSION['user_id'];
    $receiver_email = $_POST['receiver_email'];
    $message = $_POST['message'];

    // Get receiver ID from email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $receiver_email);
    $stmt->execute();
    $stmt->bind_result($receiver_id);
    $stmt->fetch();
    $stmt->close();

    if ($receiver_id) {
        $stmt = $conn->prepare("INSERT INTO notifications (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
        $stmt->execute();
        echo "Notification sent!";
    } else {
        echo "Receiver not found.";
    }
}
?>
<form method="POST">
    <input type="email" name="receiver_email" placeholder="Receiver's Email" required><br>
    <textarea name="message" placeholder="Type message here..." required></textarea><br>
    <button type="submit">Send Notification</button>
</form>
