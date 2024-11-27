<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php'; // Restrict access to logged-in voters

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Fetch active elections (i.e., elections happening now)
$elections = $pdo->query("SELECT * FROM elections WHERE CURDATE() BETWEEN start_date AND end_date")->fetchAll();

// Handle vote submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $candidate_id = $_POST['candidate_id'];
    $election_id = $_POST['election_id'];

    // Ensure the user hasn't already voted in this election
    $stmt = $pdo->prepare("SELECT * FROM votes WHERE voter_id = ? AND election_id = ?");
    $stmt->execute([$_SESSION['user_id'], $election_id]);

    if ($stmt->rowCount() > 0) {
        // If the user has already voted, show an error
        $error = "You have already voted in this election!";
    } else {
        // Record the vote
        $stmt = $pdo->prepare("INSERT INTO votes (voter_id, candidate_id, election_id) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $candidate_id, $election_id]);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Bundle with JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Cast Your Vote</h2>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

        <?php foreach ($elections as $election): ?>
            <h3><?= htmlspecialchars($election['title']) ?> (Voting Period: <?= htmlspecialchars($election['start_date']) ?> - <?= htmlspecialchars($election['end_date']) ?>)</h3>
            <form method="POST">
                <input type="hidden" name="election_id" value="<?= $election['id'] ?>">

                <h4>Choose a Candidate:</h4>
                <ul class="list-group">
                    <?php
                    // Fetch only approved candidates for this election
                    $candidates = $pdo->prepare("SELECT * FROM candidates WHERE election_id = ? AND status = 'approved'");
                    $candidates->execute([$election['id']]);
                    if ($candidates->rowCount() > 0) {
                        foreach ($candidates as $candidate):
                    ?>
                        <li class="list-group-item">
                            <label>
                                <input type="radio" name="candidate_id" value="<?= $candidate['id'] ?>" required>
                                <?= htmlspecialchars($candidate['name']) ?> - <?= htmlspecialchars($candidate['position']) ?>
                                <br>
                                <small>Manifesto: <?= htmlspecialchars($candidate['manifesto']) ?></small>
                            </label>
                        </li>
                    <?php
                        endforeach;
                    } else {
                        echo "<li class='list-group-item'>No approved candidates available for this election.</li>";
                    }
                    ?>
                </ul>

                <button type="submit" class="btn btn-primary mt-3">Submit Vote</button>
            </form>
        <?php endforeach; ?>
    </div>
</body>
</html>
