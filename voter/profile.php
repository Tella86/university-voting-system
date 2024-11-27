<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php'; // Restrict access to logged-in users

// Ensure only voters can access
if (!is_voter()) {
    header("Location: ../login.php");
    exit;
}

// Fetch user information
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Fetch user's voting history
$voting_history = $pdo->prepare("
    SELECT e.title AS election_title, c.name AS candidate_name, c.photo AS candidate_photo
    FROM votes v
    JOIN elections e ON v.election_id = e.id
    JOIN candidates c ON v.candidate_id = c.id
    WHERE v.voter_id = ?
");
$voting_history->execute([$_SESSION['user_id']]);
$history = $voting_history->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Profile</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Bundle with JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Your Profile</h2>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($user['name']) ?></h5>
                <p class="card-text">
                    <strong>Email:</strong> <?= htmlspecialchars($user['email']) ?><br>
                    <strong>Role:</strong> <?= htmlspecialchars(ucfirst($user['role'])) ?><br>
                    <strong>Registered Since:</strong> <?= htmlspecialchars($user['created_at']) ?>
                </p>
            </div>
        </div>

        <h3>Your Voting History</h3>
        <?php if (count($history) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Election</th>
                        <th>Voted For</th>
                        <th>Candidate Photo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $vote): ?>
                        <tr>
                            <td><?= htmlspecialchars($vote['election_title']) ?></td>
                            <td><?= htmlspecialchars($vote['candidate_name']) ?></td>
                            <td>
                                <img src="../assets/images/<?= htmlspecialchars(basename($vote['candidate_photo'])) ?>" 
                                     alt="Candidate Photo" 
                                     style="width: 100px; height: auto;">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have not participated in any elections yet.</p>
        <?php endif; ?>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
