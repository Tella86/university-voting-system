<?php
include '../includes/db.php';
include '../includes/auth.php'; // Ensures only admins can access

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $stmt = $pdo->prepare("INSERT INTO elections (title, start_date, end_date) VALUES (?, ?, ?)");
    $stmt->execute([$title, $start_date, $end_date]);
}
$elections = $pdo->query("SELECT * FROM elections")->fetchAll();
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
        <form method="POST">
            <div class="mb-3">
                <label>Title</label>
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
            <button type="submit" class="btn btn-success">Create Election</button>
        </form>
        <h3 class="mt-5">Existing Elections</h3>
        <ul>
            <?php foreach ($elections as $election): ?>
                <li><?= htmlspecialchars($election['title']) ?> (<?= $election['start_date'] ?> to <?= $election['end_date'] ?>)</li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
