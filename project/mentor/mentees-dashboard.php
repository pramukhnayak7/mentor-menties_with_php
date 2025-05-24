<?php
session_start();
require '../common/db.php';

// Ensure user is logged in

// Fetch all students with year
$stmt = $conn->prepare("
    SELECT users.id, users.username, users.email, students.name, students.year
    FROM users
    JOIN students ON users.username = students.usn
    ORDER BY students.year ASC
");
$stmt->execute();
$result = $stmt->get_result();

// Fetch distinct years
$stmt_years = $conn->prepare("SELECT DISTINCT year FROM students ORDER BY year ASC");
$stmt_years->execute();
$years_result = $stmt_years->get_result();
$years = [];
while ($year_row = $years_result->fetch_assoc()) {
    $years[] = $year_row['year'];
}
$stmt_years->close();

// Handle notification form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = filter_input(INPUT_POST, 'receiver_id', FILTER_VALIDATE_INT);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    if ($receiver_id && $message) {
        $stmt = $conn->prepare("INSERT INTO notifications (sender_id, receiver_id, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $sender_id, $receiver_id, $message);
        if ($stmt->execute()) {
            echo "<script>alert('Notification sent!'); window.location.href=window.location.href;</script>";
        } else {
            echo "<script>alert('Error sending notification.');</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('Invalid input.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentees Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Global Styles */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        body.dark-mode {
            background-color: #222;
            color: #eee;
        }

        /* Header Styles */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: #007bff;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
        }

        .header .pagetitle {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 700;
        }

        .navbar {
            display: none;
        }

        .navbar.active {
            display: flex;
            flex-direction: column;
            position: absolute;
            top: 60px;
            right: 20px;
            background: rgba(0, 123, 255, 0.8);
            padding: 10px;
            border-radius: 8px;
            backdrop-filter: blur(8px);
        }

        .navbar a {
            color: #fff;
            padding: 8px 0;
            text-decoration: none;
            font-size: 1rem;
        }

        #menu-icon {
            font-size: 1.8rem;
            color: #fff;
            cursor: pointer;
        }

        @media (min-width: 768px) {
            .navbar {
                display: flex;
                flex-direction: row;
                position: static;
                background: transparent;
                padding: 0;
            }

            .navbar a {
                margin: 0 15px;
                font-size: 1.1rem;
            }

            #menu-icon {
                display: none;
            }
        }

        /* Dashboard Styles */
        .dashboard {
            margin-top: 80px;
            padding: 20px;
            min-height: 100vh;
        }

        .team-title {
            text-align: center;
            color: #333;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            position: relative;
        }

        body.dark-mode .team-title {
            color: #fff;
        }

        .team-title::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background: #0ef;
        }

        /* Profile Card Styles */
        .profile-card {
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }

        body.dark-mode .profile-card {
            background-color: #333;
            color: #eee;
        }

        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .profile-card img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            margin: 15px auto;
            transition: transform 0.3s ease;
        }

        .profile-card:hover img {
            transform: scale(1.05);
        }

        .profile-card::before {
            content: '';
            position: absolute;
            top: -100%;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(14, 255, 255, 0.2) 0%, transparent 100%);
            transition: top 0.5s ease;
            z-index: 1;
        }

        .profile-card:hover::before {
            top: 0;
        }

        .profile-info {
            position: relative;
            z-index: 2;
            text-align: center;
            padding: 15px;
        }

        /* Action Links */
        .action-links {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 10px;
        }

        .action-links .btn {
            font-size: 0.9rem;
            padding: 8px 12px;
        }

        /* Filter Controls */
        .filter-controls {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .form-select {
            max-width: 200px;
            border-radius: 25px;
            padding: 8px 15px;
            border: 2px solid #ddd;
            transition: all 0.3s ease;
        }

        .form-select:focus {
            border-color: #0ef;
            box-shadow: 0 0 8px rgba(14, 255, 255, 0.5);
        }

        /* Modal Styles */
        .modal {
            z-index: 1055;
        }

        .modal-backdrop {
            z-index: 1050;
        }

        /* Animations */
        .team-members {
            opacity: 0;
            animation: fadeInUp 0.8s forwards;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .dashboard.loaded {
            animation: fadeIn 0.8s ease forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transformreport translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <span class="pagetitle">MENTEES DASHBOARD</span>
        <i class="fa fa-bars" id="menu-icon"></i>
        <nav class="navbar" id="navbar">
            <a href="mentor.html">About Mentor</a>
            <a href="mentees-dashboard.php">Dashboard</a>
            <a href="mentor-profile.html">Profile</a>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="dashboard">
        <h2 class="team-title">mentees</h2>
          <form action="register.php" method="post">
    <button type="submit">Add User</button>
</form>

        <!-- Year Filter -->
        <div class="filter-controls">
            <select class="form-select" id="yearSelect" aria-label="Filter by year">
                <option value="">All Years</option>
                <?php foreach ($years as $year): ?>
                    <option value="<?php echo htmlspecialchars($year); ?>">Year <?php echo htmlspecialchars($year); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Team Members -->
        <section class="team-section">
            <div class="team-members row">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4 profile-card" data-year="<?php echo htmlspecialchars($row['year']); ?>">
                        <div class="card h-100">
                            <img src="https://university-student-photos.s3.ap-south-1.amazonaws.com/049/student_photos/<?php echo htmlspecialchars(strtoupper($row['username'])); ?>.JPG"
                                 alt="Profile" class="card-img-top rounded-circle">
                            <div class="card-body profile-info">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                                <p class="card-text">Username: <?php echo htmlspecialchars($row['username']); ?></p>
                                <p class="card-text">Email: <?php echo htmlspecialchars($row['email']); ?></p>
                                <div class="action-links">
                                    <a href="index.php?id=<?php echo $row['id']; ?>&usn=<?php echo htmlspecialchars($row['username']); ?>"
                                       class="btn btn-primary">View</a>
                                    <a href="delete.php?id=<?php echo $row['id']; ?>"
                                       class="btn btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                                    <button type="button"
                                            class="btn btn-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#messageModal<?php echo $row['id']; ?>">Send Message</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message Modal -->
                    <div class="modal fade"
                         id="messageModal<?php echo $row['id']; ?>"
                         tabindex="-1"
                         aria-labelledby="messageModalLabel<?php echo $row['id']; ?>"
                         aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="messageModalLabel<?php echo $row['id']; ?>">
                                        Send Message to <?php echo htmlspecialchars($row['name']); ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" action="">
                                    <div class="modal-body">
                                        <input type="hidden" name="receiver_id" value="<?php echo $row['id']; ?>">
                                        <div class="mb-3">
                                            <textarea name="message"
                                                      class="form-control"
                                                      rows="4"
                                                      placeholder="Type your message..."
                                                      required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Send</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
                <?php $result->close(); ?>
            </div>
        </section>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize on DOM load
        document.addEventListener('DOMContentLoaded', () => {
            // Add loaded class for animation
            document.querySelector('.dashboard').classList.add('loaded');

            // Initialize Bootstrap modals
            document.querySelectorAll('.modal').forEach(modal => {
                new bootstrap.Modal(modal);
            });

            // Debug modal triggers
            document.querySelectorAll('[data-bs-toggle="modal"]').forEach(button => {
                button.addEventListener('click', () => {
                    console.log(`Opening modal: ${button.dataset.bsTarget}`);
                });
            });

            // Menu toggle
            const menuIcon = document.getElementById('menu-icon');
            const navbar = document.getElementById('navbar');
            menuIcon.addEventListener('click', () => {
                navbar.classList.toggle('active');
            });

            // Year filter
            const yearSelect = document.getElementById('yearSelect');
            const profileCards = document.querySelectorAll('.profile-card');
            yearSelect.addEventListener('change', () => {
                const selectedYear = yearSelect.value;
                profileCards.forEach(card => {
                    card.style.display = (selectedYear === '' || card.dataset.year === selectedYear)
                        ? 'block'
                        : 'none';
                });
            });
        });
    </script>
</body>
</html>
