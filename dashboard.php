<?php
session_start();
include 'includes/auth.php'; // Ensure only admins access
include 'includes/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Admin Dashboard</h2>
        <a href="admin/manage_elections.php" class="btn btn-primary">Manage Elections</a>
        <a href="admin/approve_candidates.php" class="btn btn-secondary">Approve Candidates</a>
        <a href="admin/results.php" class="btn btn-success">View Results</a>
    </div>
</body>
</html>
