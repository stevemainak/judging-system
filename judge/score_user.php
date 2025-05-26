<?php
include '../db_connect.php';
session_start();

// Handle judge login
if (isset($_POST['judge_login'])) {
    $code = trim($_POST['judge_code']);
    $stmt = $conn->prepare("SELECT * FROM judges WHERE judge_code = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $judge = $result->fetch_assoc();
        $_SESSION['judge_id'] = $judge['id'];
        $_SESSION['judge_name'] = $judge['display_name'];
    } else {
        $error = "Invalid Judge Code";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle score submission (insert or update)
if (isset($_POST['submit_scores'])) {
    $judge_id = $_SESSION['judge_id'] ?? 0;

    if ($judge_id) {
        $all_users = $conn->query("SELECT id FROM users");
        $user_count = $all_users->num_rows;
        $submitted_scores = $_POST['scores'] ?? [];

        // Count how many scores already exist for this judge
        $check = $conn->prepare("SELECT COUNT(*) AS total FROM scores WHERE judge_id = ?");
        $check->bind_param("i", $judge_id);
        $check->execute();
        $result = $check->get_result();
        $row = $result->fetch_assoc();
        $existing_score_count = $row['total'];

        // If already submitted full scores, block new submission (only allow update)
        if ($existing_score_count === $user_count) {
            // Validate input scores for editing/updating
            $valid = true;
            foreach ($submitted_scores as $user_id => $score) {
                if ($score === '' || !is_numeric($score) || $score < 0 || $score > 100) {
                    $valid = false;
                    break;
                }
            }
            if (!$valid) {
                $_SESSION['score_error'] = "Please ensure all scores are filled and between 0–100.";
            } else {
                // Update existing scores
                foreach ($submitted_scores as $user_id => $score) {
                    $score = (int)$score;
                    $stmt = $conn->prepare("UPDATE scores SET score = ? WHERE judge_id = ? AND user_id = ?");
                    $stmt->bind_param("iii", $score, $judge_id, $user_id);
                    $stmt->execute();
                }
                $_SESSION['score_message'] = "Scores updated successfully.";
            }
        } else {
            // For first time submission, check all users scored
            if (count($submitted_scores) < $user_count) {
                $_SESSION['score_error'] = "You must score ALL participants before submitting.";
            } else {
                // Validate inputs
                $valid = true;
                foreach ($submitted_scores as $user_id => $score) {
                    if ($score === '' || !is_numeric($score) || $score < 0 || $score > 100) {
                        $valid = false;
                        break;
                    }
                }
                if (!$valid) {
                    $_SESSION['score_error'] = "Please ensure all scores are filled and between 0–100.";
                } else {
                    // Insert or update scores (REPLACE INTO)
                    foreach ($submitted_scores as $user_id => $score) {
                        $score = (int)$score;
                        $stmt = $conn->prepare("REPLACE INTO scores (judge_id, user_id, score) VALUES (?, ?, ?)");
                        $stmt->bind_param("iii", $judge_id, $user_id, $score);
                        $stmt->execute();
                    }
                    $_SESSION['score_message'] = "Scores submitted successfully.";
                }
            }
        }
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Logout judge
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Judge Panel – Score Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container-fluid justify-content-between">
            <a href="../index.php" class="btn btn-outline-light">⬅ Back to Home</a>
            <span class="navbar-brand mx-auto fs-3">🧑‍⚖️ Judge Panel – Score Users</span>
            <div>
                <?php if (isset($_SESSION['judge_id'])): ?>
                    <a href="?view_tally=true" class="btn btn-outline-info me-2">📊 View Tally</a>
                    <a href="?logout=true" class="btn btn-outline-danger">Logout</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (!isset($_SESSION['judge_id'])): ?>
            <div class="card mx-auto" style="max-width: 400px;">
                <div class="card-body">
                    <h5 class="card-title">Enter Judge Code</h5>
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <input type="text" name="judge_code" class="form-control" placeholder="Enter Judge Code" required>
                        </div>
                        <button type="submit" name="judge_login" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <div class="mb-3">
                <h5>Welcome, <?php echo htmlspecialchars($_SESSION['judge_name']); ?></h5>
            </div>

            <?php if (!empty($_SESSION['score_message'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['score_message'];
                                                    unset($_SESSION['score_message']); ?></div>
            <?php endif; ?>

            <?php if (!empty($_SESSION['score_error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['score_error'];
                                                unset($_SESSION['score_error']); ?></div>
            <?php endif; ?>

            <?php if (isset($_GET['view_tally'])): ?>
                <h4>Your Submitted Scores</h4>
                <?php
                $judge_id = $_SESSION['judge_id'];
                $per_page = 10;
                $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                $offset = ($page - 1) * $per_page;

                $count_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM scores WHERE judge_id = ?");
                $count_stmt->bind_param("i", $judge_id);
                $count_stmt->execute();
                $count_result = $count_stmt->get_result();
                $total_rows = $count_result->fetch_assoc()['total'];
                $total_pages = ceil($total_rows / $per_page);

                $query = "
                    SELECT u.full_name, s.score 
                    FROM scores s 
                    JOIN users u ON s.user_id = u.id 
                    WHERE s.judge_id = ? 
                    ORDER BY s.score DESC 
                    LIMIT ? OFFSET ?
                ";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("iii", $judge_id, $per_page, $offset);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0): ?>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                    <td><?php echo $row['score']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <!-- Pagination Links -->
                    <nav>
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?view_tally=true&page=<?php echo $page - 1; ?>">Previous</a>
                                </li>
                            <?php endif; ?>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php if ($i === $page) echo 'active'; ?>">
                                    <a class="page-link" href="?view_tally=true&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?view_tally=true&page=<?php echo $page + 1; ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>

                <?php else: ?>
                    <div class="alert alert-warning">You haven't submitted any scores yet.</div>
                <?php endif; ?>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-secondary mt-3">Back to Score Users</a>

            <?php else: ?>
                <?php
                // Fetch all users to display scoring inputs
                $users = $conn->query("SELECT id, full_name FROM users ORDER BY full_name ASC");
                $judge_id = $_SESSION['judge_id'];

                // Count how many scores judge has submitted
                $count_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM scores WHERE judge_id = ?");
                $count_stmt->bind_param("i", $judge_id);
                $count_stmt->execute();
                $count_result = $count_stmt->get_result();
                $total_submitted = $count_result->fetch_assoc()['total'];

                // Count total users
                $all_users = $conn->query("SELECT id FROM users");
                $total_users = $all_users->num_rows;

                // Determine if judge already submitted all scores
                $readonly_mode = ($total_submitted === $total_users);
                ?>

                <?php if ($readonly_mode): ?>
                    <div class="alert alert-info">
                        You have already submitted scores for all participants. You can update your scores below.
                    </div>
                <?php endif; ?>

                <form method="POST" class="mt-4">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-secondary">
                            <tr>
                                <th>#</th>
                                <th>User Name</th>
                                <th>Your Score (0–100)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 1;
                            while ($user = $users->fetch_assoc()):
                                // Fetch existing score for this user & judge
                                $stmt = $conn->prepare("SELECT score FROM scores WHERE judge_id = ? AND user_id = ?");
                                $stmt->bind_param("ii", $judge_id, $user['id']);
                                $stmt->execute();
                                $score_result = $stmt->get_result();
                                $existing_score = $score_result->num_rows > 0 ? $score_result->fetch_assoc()['score'] : '';
                            ?>
                                <tr>
                                    <td><?php echo $count++; ?></td>
                                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                    <td>
                                        <input type="number" min="0" max="100" class="form-control"
                                            name="scores[<?php echo $user['id']; ?>]"
                                            value="<?php echo htmlspecialchars($existing_score); ?>"
                                            required>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <button type="submit" name="submit_scores" class="btn btn-primary">
                        <?php echo $readonly_mode ? "Update Scores" : "Submit Scores"; ?>
                    </button>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </div>

</body>

</html>