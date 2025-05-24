<?php
session_start();
require 'db.php';
//if (!isset($_SESSION['user_id'])) {
 //   header("Location: ../index.html");
 //   exit();
//}

$user_id = $_SESSION['user_id'];
$username = strtoupper($_SESSION['username']);

// Prepare students data grouped by year
$query = "SELECT name, usn, email, year FROM students ORDER BY year";
$result = $conn->query($query);
$studentsByYear = [];

while ($row = $result->fetch_assoc()) {
    $year = $row['year'];
    if (!isset($studentsByYear[$year])) {
        $studentsByYear[$year] = [];
    }
    $studentsByYear[$year][] = [
        'name' => $row['name'],
        'usn' => $row['usn'],
        'email' => $row['email'],
        'img' => "https://university-student-photos.s3.ap-south-1.amazonaws.com/049/student_photos%2F" . strtoupper($row['usn']) . ".JPG"
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mentor Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background: #f8f9fa;
    }

    header {
      background: #007bff;
      color: #fff;
      padding: 1rem 2rem;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .logo {
      font-size: 1.5rem;
      font-weight: bold;
    }

    .navbar-nav .nav-link {
      color: #fff;
    }

    .navbar-nav .nav-link:hover {
      color: #d1ecf1;
    }

    .heading {
      text-align: center;
      margin: 2rem 0;
      color: #343a40;
    }

    .student-img {
      width: 200px;
      height: 200px;
      object-fit: cover;
      border-radius: 10px;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }

    .student-info {
      text-align: center;
      margin-top: 0.5rem;
    }

    .student-card {
      background: #ffffff;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      padding: 1rem;
      max-width: 230px;
      text-align: center;
      margin: 10px;
    }

    h4 {
      margin-top: 3rem;
      color: #0d6efd;
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
      background-color: blue;
      border-radius: 50%;
    }

    .form-select {
      background-color: #ccd0d4;
      color: white;
      border: 1px solid #007bff;
      border-radius: 0.25rem;
    }

    .form-select:focus {
      background-color: #0056b3;
      color: white;
      border-color: #0056b3;
    }

    .form-select option {
      background-color: #007bff;
      color: white;
    }
  </style>
</head>
<body>

  <!-- Navigation Bar -->
  <header class="d-flex justify-content-between align-items-center">
    <div class="logo">Mentor Dashboard</div>
    <nav class="navbar navbar-expand-lg navbar-dark">
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="#">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Profile</a>
          </li>
        </ul>
      </div>
    </nav>
  </header>

  <main class="container">
    <h2 class="heading">Student List</h2>

    <div class="mb-4">
      <label for="yearSelect" class="form-label">Select Year</label>
      <select class="form-select" id="yearSelect"></select>
    </div>

    <div id="carouselContainer"></div>
  </main>

  <script>
    const studentsByYear = <?php echo json_encode($studentsByYear); ?>;

    const yearSelect = document.getElementById("yearSelect");
    const carouselContainer = document.getElementById("carouselContainer");

    function renderCarousel(year) {
      const students = studentsByYear[year];
      const carouselId = `carousel-${year}`;

      let slides = '';
      students.forEach((student, index) => {
        slides += `
          <div class="carousel-item ${index === 0 ? 'active' : ''}">
            <div class="d-flex justify-content-center">
              <div class="student-card">
                <img src="${student.img}" class="student-img" alt="${student.name}">
                <div class="student-info">
                  <strong>Name:</strong> ${student.name}<br>
                  <strong>USN:</strong> ${student.usn}<br>
                  <strong>Email:</strong> ${student.email}
                </div>
              </div>
            </div>
          </div>
        `;
      });

      carouselContainer.innerHTML = `
        <h4>${year}</h4>
        <div id="${carouselId}" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner">
            ${slides}
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#${carouselId}" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#${carouselId}" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
        </div>
      `;
    }

    // Populate dropdown
    Object.keys(studentsByYear).forEach((year, idx) => {
      const option = document.createElement("option");
      option.value = year;
      option.text = year;
      if (idx === 0) option.selected = true;
      yearSelect.appendChild(option);
    });

    // Initial render
    renderCarousel(yearSelect.value);

    // Listen for dropdown changes
    yearSelect.addEventListener("change", () => {
      renderCarousel(yearSelect.value);
    });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
