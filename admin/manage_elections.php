<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php'; // Restrict access to admins only

// Handle election creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $stmt = $pdo->prepare("INSERT INTO elections (title, start_date, end_date) VALUES (?, ?, ?)");
    $stmt->execute([$title, $start_date, $end_date]);
    $success = "Election created successfully.";
}

// Fetch all elections
$elections = $pdo->query("SELECT * FROM elections ORDER BY start_date DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Elections</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Manage Elections</h2>
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label>Election Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Start Date</label>
                <input type="date" name="start_date" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>End Date</label>
                <input type="date" name="end_date" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Election</button>
        </form>

        <h3>Existing Elections</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($elections as $election): ?>
                    <tr>
                        <td><?= htmlspecialchars($election['title']) ?></td>
                        <td><?= $election['start_date'] ?></td>
                        <td><?= $election['end_date'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
