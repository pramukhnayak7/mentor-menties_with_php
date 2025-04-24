<?php
session_start();
include '../common/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];




$check = $conn->prepare("SELECT id FROM students WHERE user_id=?");
$check->bind_param("i",$user_id);
$check->execute();
$result = $check->get_result();
 $record = [];
if ($result->num_rows > 0)
{
    $row = $result->fetch_assoc();
    $student_id = $row['id'];
    $record = [
        'name' => $row['name'] ?? '',
        'usn' => $row['usn'] ?? '',
        'dob' => $row['dob'] ?? '',
        'sslc_marks' => $row['sslc_marks'] ?? '',
        'puc_marks' => $row['puc_marks'] ?? '',
        'email' => $row['email'] ?? '',
        'place' => $row['place'] ?? '',
        'hobbies' => $row['hobbies'] ?? '',
        'father_name' => $row['father_name'] ?? '',
        'mother_name' => $row['mother_name'] ?? '',
        'father_job' => $row['father_job'] ?? '',
        'mother_job' => $row['mother_job'] ?? '',
        'address' => $row['address'] ?? '',
        'father_phone' => $row['father_phone'] ?? '',
        'mother_phone' => $row['mother_phone'] ?? ''
    ];
}
 else {
   
    $record = [
        'name' => '',
        'usn' => '',
        'dob' => '',
        'sslc_marks' => '',
        'puc_marks' => '',
        'email' => '',
        'place' => '',
        'hobbies' => '',
        'father_name' =>'',
        'mother_name' =>'',
        'father_job' => '',
        'mother_job' => '',
        'address' => '',
        'father_phone' => '',
        'mother_phone' => ''
    ];
    $student_id = null;
}
if($_SERVER['REQUEST_METHOD']=='POST')
{
    $name = $_POST['name'];  
    $usn = $_POST['usn']; 
    $dob = $_POST['dob']; 
    $sslc_marks = $_POST['sslc_marks'];
    $puc_marks = $_POST['puc_marks'];
    $email = $_POST['email'];
    $place = $_POST['place'];
    $hobbies = $_POST['hobbies'];  
    $father_name  = $_POST['father_name'];
    $mother_name  = $_POST['mother_name'];
    $father_job  = $_POST['father_job'];
    $mother_job  = $_POST['mother_job'];
    $adress  = $_POST['address'];
    $father_phone = $_POST['father_phone'];
    $mother_phone  = $_POST['mother_phone']; 
}

if ($student_id) {
    // Update
    $stmt = $conn->prepare("UPDATE students SET name = ?, usn = ?, dob = ?, sslc_marks = ?, puc_marks = ?, email = ?, place = ?, hobbies = ?,father_name = ?, mother_name = ?, father_job = ?, mother_job = ?, address = ?, father_phone = ?, mother_phone = ? WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ssssissssssssiiii", $name, $usn, $dob, $sslc_marks, $puc_marks, $email, $place, $hobbies, $father_name, $mother_name, $father_job, $mother_job, $adress, $father_phone, $mother_phone, $student_id, $user_id);
 
} else {
    // Insert
    $stmt = $conn->prepare("INSERT INTO students (user_id, name, usn, dob, sslc_marks, puc_marks, email, place, hobbies, father_name, mother_name, father_job, mother_job, address, father_phone, mother_phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?,?,?,?,?)");
    $stmt->bind_param("isssiissssssssii", $user_id, $name, $usn, $dob, $sslc_marks, $puc_marks, $email, $place, $hobbies, $father_name, $mother_name, $father_job, $mother_job, $adress, $father_phone, $mother_phone);
   
}



$stmt->execute();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>alter studentbdata</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    
<button type="button" onclick="window.location.href='index.php'">Go to Home</button>

<form method="POST" action="alterstudentprofile.php">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" value="<?=$record['name'] ?>" required><br><br>
    <label for="usn">USN:</label>
    <input type="text" id="usn" name="usn" value="<?=$record['usn'] ?>" required><br><br>
    <label for="dob">D.O.B:</label>
    <input type="date" id="dob" name="dob" value="<?=$record['dob'] ?>" required><br><br>
    <label for="sslc_marks">SSLC MARKS:</label>
    <input type="number" id="sslc_marks" name="sslc_marks" value="<?=$record['sslc_marks'] ?>" required><br><br>
    <label for="puc_marks">PUC MARKS:</label>
    <input type="number" id="puc_marks" name="puc_marks" value="<?=$record['puc_marks'] ?>" required><br><br>
    <label for="email">EMAIL:</label>
    <input type="email" id="email" name="email" value="<?=$record['email'] ?>" required><br><br>
    <label for="place">PLACE:</label>
    <input type="text" id="place" name="place" value="<?=$record['place'] ?>" required><br><br>
    <label for="hobbies">HOBBIES:</label>
    <input type="text" id="hobbies" name="hobbies" value="<?=$record['hobbies'] ?>" required><br><br>
    <label for="father_name">FATHER NAME:</label>
    <input type="text" id="father_name" name="father_name" value="<?=$record['father_name'] ?>" required><br><br>
    <label for="mother_name">MOTHER NAME:</label>
    <input type="text" id="mother_name" name="mother_name" value="<?=$record['mother_name'] ?>" required><br><br>
    <label for="father_job">FATHER JOB:</label>
    <input type="text" id="father_job" name="father_job" value="<?=$record['father_job'] ?>" required><br><br>
    <label for="mother_job">MOTHER JOB:</label>
    <input type="text" id="mother_job" name="mother_job" value="<?=$record['mother_job'] ?>" required><br><br>
    <label for="address">ADDRESS:</label>
    <input type="text" id="address" name="address" value="<?=$record['address'] ?>" required><br><br>
    <label for="father_phone">FATHER PHONE:</label>
    <input type="number" id="father_phone" name="father_phone" value="<?=$record['father_phone'] ?>" required><br><br>
    <label for="mother_phone">MOTHER_PHONE:</label>
    <input type="number" id="mother_phone" name="mother_phone" value="<?=$record['mother_phone'] ?>" required><br><br>
    <button type="submit">Update Record</button>
</form>

</body>
</html>   
