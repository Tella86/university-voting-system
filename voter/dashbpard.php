<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php'; // Ensure the user is logged in

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Fetch active elections (i.e., elections happening now)
$elections = $pdo->query("SELECT * FROM elections WHERE CURDATE() BETWEEN start_date AND end_date")->fetchAll();

// Fetch already voted elections for the logged-in voter
$voted_elections = $pdo->prepare("SELECT DISTINCT election_id FROM votes WHERE voter_id = ?");
$voted_elections->execute([$_SESSION['user_id']]);
$voted_election_ids = $voted_elections->fetchAll(PDO::FETCH_COLUMN, 0);

// Handle vote submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $candidate_id = $_POST['candidate_id'];
    $election_id = $_POST['election_id'];

    // Ensure the user hasn't already voted in this election
    if (in_array($election_id, $voted_election_ids)) {
        // If the user has already voted, show an error
        $error = "You have already voted in this election!";
    } else {
        // Record the vote
        $stmt = $pdo->prepare("INSERT INTO votes (voter_id, candidate_id, election_id) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $candidate_id, $election_id]);
        $success = "Your vote has been successfully recorded.";
        // Add the election to the list of voted elections
        $voted_election_ids[] = $election_id;  // Add the newly voted election to the array
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Dashboard</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Bundle with JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Welcome to Your Voter Dashboard</h2>

        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>

        <?php if (count($elections) > 0): ?>
            <h3>Active Elections</h3>
            <ul class="list-group">
                <?php foreach ($elections as $election): ?>
                    <li class="list-group-item">
                        <h5><?= htmlspecialchars($election['title']) ?> (Voting Period: <?= htmlspecialchars($election['start_date']) ?> - <?= htmlspecialchars($election['end_date']) ?>)</h5>
                        <p><strong>Status:</strong> 
                            <?php if (in_array($election['id'], $voted_election_ids)): ?>
                                <span class="badge bg-success">You have voted</span>
                            <?php else: ?>
                                <span class="badge bg-warning">You can still vote</span>
                            <?php endif; ?>
                        </p>
                        
                        <?php if (!in_array($election['id'], $voted_election_ids)): ?>
                            <form method="POST">
                                <input type="hidden" name="election_id" value="<?= $election['id'] ?>">
                                <h4>Choose a Candidate:</h4>
                                <ul class="list-group">
                                    <?php
                                    // Fetch approved candidates for this election
                                    $candidates = $pdo->prepare("SELECT * FROM candidates WHERE election_id = ? AND status = 'approved'");
                                    $candidates->execute([$election['id']]);
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
                                    <?php endforeach; ?>
                                </ul>
                                <button type="submit" class="btn btn-primary mt-3">Submit Vote</button>
                            </form>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="alert alert-warning">There are no active elections at the moment.</div>
        <?php endif; ?>
    </div>
</body>
</html>
