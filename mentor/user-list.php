<?php
session_start();
require '../common/db.php';

// Fetch all users
$result = $conn->query("SELECT id, username, email FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User List</title>
  <style>
    table {
      border-collapse: collapse;
      width: 80%;
      margin: 20px auto;
    }
    th, td {
      padding: 10px;
      border: 1px solid #ddd;
      text-align: center;
    }
    a {
      color: red;
    }
  </style>
</head>
<body>
  <h2 style="text-align:center;">User List</h2>
  <table>
    <tr>
      <th>ID</th>
      <th>Username</th>
      <th>Email</th>
      <th>Action</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) : ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['username']) ?></td>
        <td><?= htmlspecialchars($row['email']) ?></td>
        <td>
          <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
</body>
</html>
