<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch records
$stmt = $conn->prepare("SELECT * FROM academic_records WHERE user_id = ? ORDER BY semester ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$records_by_sem = [];
while ($row = $result->fetch_assoc()) {
    $records_by_sem[$row['semester']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Dashboard</title>
  
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<link rel="stylesheet" href="styles.css">
</head>
<body>
  <h2>Welcome to your Dashboard</h2>
  <a href="logout.php">Logout</a>
  <form action="index.php" method="post">
    <button type="submit">Go to home</button>
</form>
  <h3>Add New Record</h3>
  <form method="POST" action="save_record.php">
    <label for="semester">Semester:</label>
    <input type="number" id="semester" name="semester" required><br><br>
    <label for="course_code">Course Code:</label>
    <input type="text" id="course_code" name="course_code" required><br><br>
    <label for="course_name">Course Name:</label>
    <input type="text" id="course_name" name="course_name" required><br><br>
    <label for="attendance">Attendance (%):</label>
    <input type="number" id="attendance" name="attendance" required><br><br>
    <label for="mse1">MSE 1 Marks:</label>
    <input type="number" id="mse1" name="mse1" required><br><br>
    <label for="mse2">MSE 2 Marks:</label>
    <input type="number" id="mse2" name="mse2" required><br><br>
    <label for="see_marks">SEE Marks:</label>
    <input type="number" id="see_marks" name="see_marks" required><br><br>
    <label for="grade">Grade:</label>
    <input type="text" id="grade" name="grade" required><br><br>
    <label for="grade_point">Grade Point:</label>
    <input type="number" id="grade_point" name="grade_point" required><br><br>
    <button type="submit">Add Record</button>
  </form>

  <h3>Your Academic Records by Semester</h3>
  <?php for ($sem = 1; $sem <= 8; $sem++) : ?>
    <?php if (!isset($records_by_sem[$sem])) continue; ?>
    <section>
      <h3>Semester <?= $sem ?></h3>
      <table>
        <thead>
          <tr>
            <th>Semester</th>
            <th>Course Code</th>
            <th>Course Name</th>
            <th>Attendance</th>
            <th>MSE 1</th>
            <th>MSE 2</th>
            <th>SEE</th>
            <th>Grade</th>
            <th>Grade Point</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($records_by_sem[$sem] as $row) : ?>
            <tr>
              <td><?= $row['semester'] ?></td>
              <td><?= $row['course_code'] ?></td>
              <td><?= $row['course_name'] ?></td>
              <td><?= $row['attendance'] ?></td>
              <td><?= $row['mse1'] ?></td>
              <td><?= $row['mse2'] ?></td>
              <td><?= $row['see_marks'] ?></td>
              <td><?= $row['grade'] ?></td>
              <td><?= $row['grade_point'] ?></td>
              <td><a href="update_record.php?id=<?= $row['id'] ?>">Edit</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </section>
  <?php endfor; ?>
</body>
</html>