<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];



$username = strtoupper($_SESSION['username']); 

// Fetch student info
$stmt = $conn->prepare("SELECT name, usn, dob, sslc_marks, puc_marks, email, place, hobbies, father_name, mother_name, father_job, mother_job, address, father_phone, mother_phone FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();



// Construct the profile picture URL
$profilePicUrl = "https://university-student-photos.s3.ap-south-1.amazonaws.com/049/student_photos%2F{$username}.JPG";
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
  <!-- THIS IS FOR NOTIFICTIONS BE CAREFUL -->
      <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">Notification</button>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
  <div class="offcanvas-header">
    <h5 id="offcanvasRightLabel">Offcanvas right</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">

    <?php
  $receiver_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT u.email AS sender_email, n.message, n.created_at
                        FROM notifications n
                        JOIN users u ON n.sender_id = u.id
                        WHERE n.receiver_id = ?
                        ORDER BY n.created_at DESC");
$stmt->bind_param("i", $receiver_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Your Notifications:</h2>";
while ($row = $result->fetch_assoc()) {
    echo "<div style='border: 1px solid #000000; padding: 10px; margin: 10px 0; background-color: #f9f9f9; color: #000;'>";
    echo "<strong>From:</strong> " . htmlspecialchars($row['sender_email']) . "<br>";
    echo "<strong>Message:</strong> " . htmlspecialchars($row['message']) . "<br>";
    echo "<em>" . $row['created_at'] . "</em>";
    echo "</div>";
}
?>
  </div>
</div>
<!-- END OF NOTIFICTIONS -->

    </div>
    <div class="nav-toggle" onclick="toggleNav()">☰</div>
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


  </section>
  <form action="alterstudentprofile.php" method="post">
    <button type="submit">EDIT</button>
</form>
  <form action="dashboard.php" method="post">
    <button type="submit">MARKS ENTRY</button>
</form>



  <script src="main1.js"></script>
</body>
</html>