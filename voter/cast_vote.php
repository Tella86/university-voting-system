<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php'; // Restrict access to logged-in voters

// Fetch active elections
$elections = $pdo->query("SELECT * FROM elections WHERE CURDATE() BETWEEN start_date AND end_date")->fetchAll();

// Handle vote submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $candidate_id = $_POST['candidate_id'];

    // Ensure the user hasn't already voted
    $stmt = $pdo->prepare("SELECT * FROM votes WHERE voter_id = ? AND election_id = ?");
    $stmt->execute([$_SESSION['user_id'], $_POST['election_id']]);
    if ($stmt->rowCount() > 0) {
        $error = "You have already voted in this election!";
    } else {
        // Record the vote
        $stmt = $pdo->prepare("INSERT INTO votes (voter_id, candidate_id, election_id) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $candidate_id, $_POST['election_id']]);
        $success = "Your vote has been successfully recorded.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cast Vote</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Cast Your Vote</h2>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

        <?php foreach ($elections as $election): ?>
            <h3><?= htmlspecialchars($election['title']) ?></h3>
            <form method="POST">
                <input type="hidden" name="election_id" value="<?= $election['id'] ?>">
                <ul>
                    <?php
                    $candidates = $pdo->prepare("SELECT * FROM candidates WHERE election_id = ?");
                    $candidates->execute([$election['id']]);
                    foreach ($candidates as $candidate):
                    ?>
                        <li>
                            <label>
                                <input type="radio" name="candidate_id" value="<?= $candidate['id'] ?>" required>
                                <?= htmlspecialchars($candidate['name']) ?> - <?= htmlspecialchars($candidate['manifesto']) ?>
                            </label>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <button type="submit" class="btn btn-primary">Submit Vote</button>
            </form>
        <?php endforeach; ?>
    </div>
</body>
</html>
