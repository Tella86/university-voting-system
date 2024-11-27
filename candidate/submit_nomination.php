<?php
session_start();
include '../includes/db.php';
include '../includes/auth.php'; // Restrict access to logged-in candidates

// Ensure only candidates can access
if (!is_candidate()) {
    header("Location: ../login.php");
    exit;
}

// Fetch active elections for nomination
$elections = $pdo->query("SELECT * FROM elections WHERE CURDATE() < start_date")->fetchAll();

// Handle nomination submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $election_id = $_POST['election_id'];
    $name = $_POST['name'];
    $manifesto = $_POST['manifesto'];
    $photo = $_FILES['photo'];

    // Validate file upload
    $upload_dir = '../assets/images/';
    $file_path = $upload_dir . basename($photo['name']);
    if (move_uploaded_file($photo['tmp_name'], $file_path)) {
        $stmt = $pdo->prepare("INSERT INTO candidates (user_id, election_id, name, manifesto, photo) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $election_id, $name, $manifesto, $file_path]);
        $success = "Your nomination has been submitted successfully!";
    } else {
        $error = "Failed to upload photo. Please try again.";
    }
}
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
        <?php if (isset($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label>Select Election</label>
                <select name="election_id" class="form-select" required>
                    <?php foreach ($elections as $election): ?>
                        <option value="<?= $election['id'] ?>">
                            <?= htmlspecialchars($election['title']) ?> (Start: <?= $election['start_date'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Your Full Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Your Manifesto</label>
                <textarea name="manifesto" class="form-control" rows="5" required></textarea>
            </div>
            <div class="mb-3">
                <label>Upload Photo</label>
                <input type="file" name="photo" class="form-control" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit Nomination</button>
        </form>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
