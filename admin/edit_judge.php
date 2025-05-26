<?php
include '../db_connect.php';

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$judge_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judge_code = trim($_POST['judge_code']);
    $display_name = trim($_POST['display_name']);

    if (!empty($judge_code) && !empty($display_name)) {
        $stmt = $conn->prepare("UPDATE judges SET judge_code = ?, display_name = ? WHERE id = ?");
        $stmt->bind_param("ssi", $judge_code, $display_name, $judge_id);

        if ($stmt->execute()) {
            header("Location: add_judge.php");
            exit();
        } else {
            $error = "Failed to update judge: " . $stmt->error;
        }
    } else {
        $error = "All fields are required.";
    }
}

// Fetch existing data
$stmt = $conn->prepare("SELECT * FROM judges WHERE id = ?");
$stmt->bind_param("i", $judge_id);
$stmt->execute();
$result = $stmt->get_result();
$judge = $result->fetch_assoc();

if (!$judge) {
    die("Judge not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Judge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <a href="add_judge.php" class="btn btn-secondary mb-3">← Back to Judge Panel</a>
        <div class="card">
            <div class="card-header bg-primary text-white">
                Edit Judge
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Judge Code</label>
                        <input type="text" name="judge_code" class="form-control" value="<?= htmlspecialchars($judge['judge_code']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Display Name</label>
                        <input type="text" name="display_name" class="form-control" value="<?= htmlspecialchars($judge['display_name']) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-success">Update Judge</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>