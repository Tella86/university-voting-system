<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php';  // To ensure the user is logged in

// Ensure the user is logged in and their user_id is in the session
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session after login

// Fetch the candidate profile data, joining with the users table to get email and role
$stmt = $pdo->prepare("
    SELECT u.email, u.role, c.name, c.status, c.rejection_reason, position
    FROM users u 
    JOIN candidates c ON u.id = c.user_id 
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    // Redirect if the user is not a candidate or doesn't exist
    header("Location: ../login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidate Profile</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Bundle with JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2>Candidate Profile</h2>
        
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Name: <?= htmlspecialchars($user['name']) ?></h5>
                <p class="card-text">Email: <?= htmlspecialchars($user['email']) ?></p>
                <p class="card-text">Role: <?= htmlspecialchars($user['role']) ?></p>
                <p class="card-text">Position: <?= htmlspecialchars($user['position']) ?></p>
                <?php if ($user['role'] == 'candidate'): ?>
                    <div class="mt-3">
                        <h4>Status: 
                            <?php
                            if ($user['status'] == 'pending') {
                                echo '<span class="text-warning">Pending Approval</span>';
                            } elseif ($user['status'] == 'approved') {
                                echo '<span class="text-success">Approved</span>';
                            } else {
                                echo '<span class="text-danger">Rejected</span>';
                            }
                            ?>
                        </h4>
                        
                        <?php if ($user['status'] == 'rejected' && !empty($user['rejection_reason'])): ?>
                            <p><strong>Reason for Rejection:</strong> <?= htmlspecialchars($user['rejection_reason']) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
