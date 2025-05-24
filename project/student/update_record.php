<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['id'])) {
    $record_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Fetch record for the specific ID
    $stmt = $conn->prepare("SELECT * FROM academic_records WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $record_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $record = $result->fetch_assoc();
    } else {
        echo "Record not found.";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_code = $_POST['course_code'];
    $course_name = $_POST['course_name'];
    $attendance = $_POST['attendance'];
    $mse1 = $_POST['mse1'];
    $mse2 = $_POST['mse2'];
    $see_marks = $_POST['see_marks'];
    $grade = $_POST['grade'];
    $grade_point = $_POST['grade_point'];

    // Update the record
    $stmt = $conn->prepare("UPDATE academic_records SET course_code = ?, course_name = ?, attendance = ?, mse1 = ?, mse2 = ?, see_marks = ?, grade = ?, grade_point = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssiiiisddi", $course_code, $course_name, $attendance, $mse1, $mse2, $see_marks, $grade, $grade_point, $record_id, $user_id);

    if ($stmt->execute()) {
        header("Location: edit_marks.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Update Record</title>
</head>
<body>
  <h2>Update Record</h2>
  <form method="POST" action="update_record.php?id=<?= $record['id'] ?>">
    <label for="course_code">Course Code:</label>
    <input type="text" id="course_code" name="course_code" value="<?= $record['course_code'] ?>" required><br><br>
    <label for="course_name">Course Name:</label>
    <input type="text" id="course_name" name="course_name" value="<?= $record['course_name'] ?>" required><br><br>
    <label for="attendance">Attendance:</label>
    <input type="number" id="attendance" name="attendance" value="<?= $record['attendance'] ?>" required><br><br>
    <label for="mse1">MSE 1 Marks:</label>
    <input type="number" id="mse1" name="mse1" value="<?= $record['mse1'] ?>" required><br><br>
    <label for="mse2">MSE 2 Marks:</label>
    <input type="number" id="mse2" name="mse2" value="<?= $record['mse2'] ?>" required><br><br>
    <label for="see_marks">SEE Marks:</label>
    <input type="number" id="see_marks" name="see_marks" value="<?= $record['see_marks'] ?>" required><br><br>
    <label for="grade">Grade:</label>
    <input type="text" id="grade" name="grade" value="<?= $record['grade'] ?>" required><br><br>
    <label for="grade_point">Grade Point:</label>
    <input type="number" id="grade_point" name="grade_point" value="<?= $record['grade_point'] ?>" required><br><br>
    <button type="submit">Update Record</button>
  </form>
</body>
</html>
