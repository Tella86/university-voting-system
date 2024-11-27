<?php
include 'includes/db.php';
$results = $pdo->query("SELECT c.name, COUNT(v.id) as votes 
    FROM votes v 
    JOIN candidates c ON c.id = v.candidate_id 
    GROUP BY c.id 
    ORDER BY votes DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results</title>
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Election Results</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Candidate</th>
                    <th>Votes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $result): ?>
                    <tr>
                        <td><?= htmlspecialchars($result['name']) ?></td>
                        <td><?= $result['votes'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
