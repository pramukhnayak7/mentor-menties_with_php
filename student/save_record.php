<?php
session_start();
include '../common/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $semester = $_POST['semester'];
    $course_code = $_POST['course_code'];
    $course_name = $_POST['course_name'];
    $attendance = $_POST['attendance'];
    $mse1 = $_POST['mse1'];
    $mse2 = $_POST['mse2'];
    $see_marks = $_POST['see_marks'];
    $grade = $_POST['grade'];
    $grade_point = $_POST['grade_point'];

    $stmt = $conn->prepare("INSERT INTO academic_records (user_id, semester, course_code, course_name, attendance, mse1, mse2, see_marks, grade, grade_point) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisssiiiid", $user_id, $semester, $course_code, $course_name, $attendance, $mse1, $mse2, $see_marks, $grade, $grade_point);
    
    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
