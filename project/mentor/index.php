<?php
session_start();
include '../common/db.php';


$user_id = $_GET['id'];



$username = strtoupper($_GET['usn']);

// Fetch student info
$stmt = $conn->prepare("SELECT name, usn, dob, sslc_marks, puc_marks, email, place, hobbies, father_name, mother_name, father_job, mother_job, address, father_phone, mother_phone FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();



// Construct the profile picture URL
$profilePicUrl = "https://university-student-photos.s3.ap-south-1.amazonaws.com/049/student_photos%2F{$username}.JPG";

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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Student Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
<link rel="stylesheet" href="styles.css">
 
</head>
<body>
  <nav class="navbar">
    <div class="nav-logo">Student Portal</div>
    <div class="nav-items" id="nav-items">

    </div>
    <div class="nav-toggle" onclick="toggleNav()">☰</div>
    <a href="logout.php">Logout</a>
  </nav>
  
  <section class="profile-section">
    <h1>Student Info</h1>
    <div class="profile-container">
      <div class="profile-pic">
       
        <img src="<?= $profilePicUrl ?>" alt="Mentee Profile Picture" class="profile-image" id="profile-image">
      </div>

      <table id="student-info">
  <tr><th>Name</th><td><?= htmlspecialchars($student['name']) ?></td></tr>
  <tr><th>USN</th><td><?= htmlspecialchars($student['usn']) ?></td></tr>
  <tr><th>Date of Birth</th><td><?= htmlspecialchars($student['dob']) ?></td></tr>
  <tr><th>SSLC Marks</th><td><?= htmlspecialchars($student['sslc_marks']) ?></td></tr>
  <tr><th>PUC/DIP Marks</th><td><?= htmlspecialchars($student['puc_marks']) ?></td></tr>
  <tr><th>Email</th><td><?= htmlspecialchars($student['email']) ?></td></tr>
  <tr><th>Place of Stay</th><td><?= htmlspecialchars($student['place']) ?></td></tr>
  <tr><th>Hobbies</th><td><?= htmlspecialchars($student['hobbies']) ?></td></tr>
</table>


<table id="family-info">
  <tr><th>Father's Name</th><td><?= htmlspecialchars($student['father_name']) ?></td>
      <th>Mother's Name</th><td><?= htmlspecialchars($student['mother_name']) ?></td></tr>
  <tr><th>Father's Occupation</th><td><?= htmlspecialchars($student['father_job']) ?></td>
      <th>Mother's Occupation</th><td><?= htmlspecialchars($student['mother_job']) ?></td></tr>
  <tr><th>Address</th><td colspan="3"><?= htmlspecialchars($student['address']) ?></td></tr>
  <tr><th>Father's Phone</th><td><?= htmlspecialchars($student['father_phone']) ?></td>
      <th>Mother's Phone</th><td><?= htmlspecialchars($student['mother_phone']) ?></td></tr>
</table>


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
