<?php
include '../includes/db.php';
include '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $election_id = $_POST['election_id'];
    $name = $_POST['name'];
    $manifesto = $_POST['manifesto'];

    $stmt = $pdo->prepare("INSERT INTO candidates (election_id, name, manifesto) VALUES (?, ?, ?)");
    $stmt->execute([$election_id, $name, $manifesto]);
}
$elections = $pdo->query("SELECT * FROM elections WHERE CURDATE() < start_date")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Nomination</title>
    <link href="../assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Submit Nomination</h2>
        <form method="POST">
            <div class="mb-3">
                <label>Election</label>
                <select name="election_id" class="form-select" required>
                    <?php foreach ($elections as $election): ?>
                        <option value="<?= $election['id'] ?>"><?= htmlspecialchars($election['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Manifesto</label>
                <textarea name="manifesto" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-success">Submit</button>
        </form>
    </div>
</body>
</html>
