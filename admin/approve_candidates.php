<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php'; // Restrict access to logged-in admins

// Ensure only admins can access
if (!is_admin()) {
    header("Location: ../login.php");
    exit;
}

// Fetch pending nominations
$candidates = $pdo->query("SELECT c.id, c.name, c.manifesto, c.photo, c.election_id, e.title as election_title 
    FROM candidates c 
    JOIN elections e ON c.election_id = e.id 
    WHERE c.status = 'pending'")->fetchAll();

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $candidate_id = $_POST['candidate_id'];
    $action = $_POST['action']; // "approve" or "reject"
    $rejection_reason = isset($_POST['rejection_reason']) ? $_POST['rejection_reason'] : null;

    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE candidates SET status = 'approved' WHERE id = ?");
        $stmt->execute([$candidate_id]);
        $message = "Candidate approved successfully.";
    } elseif ($action === 'reject') {
        $stmt = $pdo->prepare("UPDATE candidates SET status = 'rejected', rejection_reason = ? WHERE id = ?");
        $stmt->execute([$rejection_reason, $candidate_id]);
        $message = "Candidate rejected and removed from the election.";
    }

    // Refresh page to reflect changes
    header("Location: approve_candidates.php?message=" . urlencode($message));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve Candidates</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2>Approve Candidates</h2>
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['message']) ?></div>
        <?php endif; ?>

        <?php if (count($candidates) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Manifesto</th>
                        <th>Photo</th>
                        <th>Election</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($candidates as $candidate): ?>
                        <tr>
                            <td><?= htmlspecialchars($candidate['name']) ?></td>
                            <td><?= htmlspecialchars($candidate['manifesto']) ?></td>
                            <td>
                                <img src="../assets/images/<?= htmlspecialchars(basename($candidate['photo'])) ?>"
                                     alt="Candidate Photo"
                                     style="width: 100px; height: auto;">
                            </td>
                            <td><?= htmlspecialchars($candidate['election_title']) ?></td>
                            <td>
                                <!-- Approve Form -->
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="candidate_id" value="<?= $candidate['id'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success">Approve</button>
                                </form>

                                <!-- Reject Form -->
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="candidate_id" value="<?= $candidate['id'] ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal<?= $candidate['id'] ?>">Reject</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal for Rejection -->
                        <div class="modal fade" id="rejectModal<?= $candidate['id'] ?>" tabindex="-1" aria-labelledby="rejectModalLabel<?= $candidate['id'] ?>" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="rejectModalLabel<?= $candidate['id'] ?>">Provide Rejection Reason</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <textarea name="rejection_reason" class="form-control" placeholder="Provide the reason for rejection" required></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-danger">Submit Rejection</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No pending nominations to review.</p>
        <?php endif; ?>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
