<?php
session_start();
require '../common/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = false;

// Fetch existing student data
$stmt = $conn->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc() ?? [];

// Initialize record with default values
$record = [
    'name' => $student['name'] ?? '',
    'usn' => $student['usn'] ?? '',
    'dob' => $student['dob'] ?? '',
    'sslc_marks' => $student['sslc_marks'] ?? '',
    'puc_marks' => $student['puc_marks'] ?? '',
    'email' => $student['email'] ?? '',
    'place' => $student['place'] ?? '',
    'hobbies' => $student['hobbies'] ?? '',
    'father_name' => $student['father_name'] ?? '',
    'mother_name' => $student['mother_name'] ?? '',
    'father_job' => $student['father_job'] ?? '',
    'mother_job' => $student['mother_job'] ?? '',
    'address' => $student['address'] ?? '',
    'father_phone' => $student['father_phone'] ?? '',
    'mother_phone' => $student['mother_phone'] ?? ''
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $required = ['name', 'usn', 'dob', 'email', 'father_name', 'mother_name', 'address'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required";
        }
    }

    // Validate email format
    if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Validate marks are numeric if provided
    if (!empty($_POST['sslc_marks']) && !is_numeric($_POST['sslc_marks'])) {
        $errors[] = "SSLC marks must be a number";
    }
    if (!empty($_POST['puc_marks']) && !is_numeric($_POST['puc_marks'])) {
        $errors[] = "PUC marks must be a number";
    }

    // If no errors, proceed with database operation
    if (empty($errors)) {
        // Prepare data - trim and sanitize
        $data = [
            'name' => trim($_POST['name']),
            'usn' => trim($_POST['usn']),
            'dob' => $_POST['dob'],
            'sslc_marks' => $_POST['sslc_marks'] ?? null,
            'puc_marks' => $_POST['puc_marks'] ?? null,
            'email' => trim($_POST['email']),
            'place' => trim($_POST['place'] ?? ''),
            'hobbies' => trim($_POST['hobbies'] ?? ''),
            'father_name' => trim($_POST['father_name']),
            'mother_name' => trim($_POST['mother_name']),
            'father_job' => trim($_POST['father_job'] ?? ''),
            'mother_job' => trim($_POST['mother_job'] ?? ''),
            'address' => trim($_POST['address']),
            'father_phone' => trim($_POST['father_phone'] ?? ''),
            'mother_phone' => trim($_POST['mother_phone'] ?? ''),
            'user_id' => $user_id
        ];

        if (!empty($student)) {
            // Update existing record
            $query = "UPDATE students SET
                name = ?, usn = ?, dob = ?, sslc_marks = ?, puc_marks = ?,
                email = ?, place = ?, hobbies = ?, father_name = ?,
                mother_name = ?, father_job = ?, mother_job = ?,
                address = ?, father_phone = ?, mother_phone = ?
                WHERE user_id = ?";

            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssdsssssssssssi",
                $data['name'], $data['usn'], $data['dob'], $data['sslc_marks'],
                $data['puc_marks'], $data['email'], $data['place'], $data['hobbies'],
                $data['father_name'], $data['mother_name'], $data['father_job'],
                $data['mother_job'], $data['address'], $data['father_phone'],
                $data['mother_phone'], $user_id);
        } else {
            // Insert new record
            $query = "INSERT INTO students (
                user_id, name, usn, dob, sslc_marks, puc_marks, email,
                place, hobbies, father_name, mother_name, father_job,
                mother_job, address, father_phone, mother_phone
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($query);
            $stmt->bind_param("isssdsssssssssss",
                $data['user_id'], $data['name'], $data['usn'], $data['dob'],
                $data['sslc_marks'], $data['puc_marks'], $data['email'],
                $data['place'], $data['hobbies'], $data['father_name'],
                $data['mother_name'], $data['father_job'], $data['mother_job'],
                $data['address'], $data['father_phone'], $data['mother_phone']);
        }

        if ($stmt->execute()) {
            $success = true;
            // Refresh the data after update
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            $errors[] = "Database error: ".$stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4 max-w-4xl">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h1 class="text-2xl font-bold mb-6 text-center">Edit Student Profile</h1>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    Profile updated successfully!
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Personal Information -->
                <div class="space-y-2">
                    <h2 class="text-lg font-semibold border-b pb-2">Personal Information</h2>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name*</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($record['name']) ?>"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">USN*</label>
                        <input type="text" name="usn" value="<?= htmlspecialchars($record['usn']) ?>"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date of Birth*</label>
                        <input type="date" name="dob" value="<?= htmlspecialchars($record['dob']) ?>"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email*</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($record['email']) ?>"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Place</label>
                        <input type="text" name="place" value="<?= htmlspecialchars($record['place']) ?>"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Hobbies</label>
                        <input type="text" name="hobbies" value="<?= htmlspecialchars($record['hobbies']) ?>"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Academic Information -->
                <div class="space-y-2">
                    <h2 class="text-lg font-semibold border-b pb-2">Academic Information</h2>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">SSLC Marks (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="sslc_marks"
                            value="<?= htmlspecialchars($record['sslc_marks']) ?>"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">PUC Marks (%)</label>
                        <input type="number" step="0.01" min="0" max="100" name="puc_marks"
                            value="<?= htmlspecialchars($record['puc_marks']) ?>"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>

                <!-- Family Information -->
                <div class="md:col-span-2 space-y-2">
                    <h2 class="text-lg font-semibold border-b pb-2">Family Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Father's Name*</label>
                            <input type="text" name="father_name" value="<?= htmlspecialchars($record['father_name']) ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mother's Name*</label>
                            <input type="text" name="mother_name" value="<?= htmlspecialchars($record['mother_name']) ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Father's Occupation</label>
                            <input type="text" name="father_job" value="<?= htmlspecialchars($record['father_job']) ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mother's Occupation</label>
                            <input type="text" name="mother_job" value="<?= htmlspecialchars($record['mother_job']) ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Father's Phone</label>
                            <input type="tel" name="father_phone" value="<?= htmlspecialchars($record['father_phone']) ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mother's Phone</label>
                            <input type="tel" name="mother_phone" value="<?= htmlspecialchars($record['mother_phone']) ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Address*</label>
                        <textarea name="address" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required><?= htmlspecialchars($record['address']) ?></textarea>
                    </div>
                </div>

                <div class="md:col-span-2 flex justify-between mt-4">
                    <a href="index.php" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                        Back to Home
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Save Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
