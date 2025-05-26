<?php include '../db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Panel – Manage Judges</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #ece9e6, #ffffff);
            padding: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Thin top navbar */
        .top-navbar {
            background-color: #f8f9fa;
            height: 30px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        /* Main blue navbar */
        .navbar-primary {
            background-color: #007bff;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.4);
            padding: 0.75rem 1rem;
        }

        .navbar-primary .navbar-brand {
            flex-grow: 1;
            text-align: center;
            font-size: 1.6rem;
            font-weight: 700;
            color: white !important;
            user-select: none;
        }

        /* Back button on the right */
        .btn-back {
            order: 2;
            margin-left: auto;
            background-color: #f8f9fa;
            color: #007bff;
            font-weight: 600;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-back:hover {
            background-color: #e2e6ea;
            color: #0056b3;
        }

        /* Container */
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 15px;
        }

        /* Table with rounded corners and shadow */
        .table {
            border-collapse: separate !important;
            border-spacing: 0 0.3rem;
            /* Reduced vertical spacing */
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgb(0 0 0 / 0.08);
            overflow: hidden;
        }

        /* Table header style */
        thead th {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            background-color: #007bff;
            color: white;
            border: none !important;
            padding: 10px 12px;
            /* Reduced padding */
            font-size: 0.9rem;
            /* Smaller font */
            user-select: none;
        }

        /* Rounded corners for first and last th */
        thead th:first-child {
            border-top-left-radius: 12px;
        }

        thead th:last-child {
            border-top-right-radius: 12px;
        }

        /* Table body rows */
        tbody tr {
            background-color: white;
            box-shadow: 0 2px 5px rgb(0 0 0 / 0.04);
            /* Reduced shadow */
            border-radius: 8px;
            /* Slightly smaller radius */
            transition: background-color 0.3s ease;
        }

        tbody tr:hover {
            background-color: #f0f7ff;
        }

        /* Table cells */
        tbody td {
            vertical-align: middle !important;
            padding: 8px 12px;
            /* Reduced padding */
            border: none !important;
            font-size: 0.85rem;
            /* Smaller font */
            color: #333;
        }

        tbody td:first-child {
            font-weight: 600;
            color: #007bff;
        }

        /* Buttons with icons only */
        .btn-action {
            border-radius: 8px;
            width: 32px;
            /* Smaller width */
            height: 32px;
            /* Smaller height */
            padding: 0;
            font-size: 1rem;
            /* Smaller font size */
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.25s ease;
        }

        .btn-warning.btn-action {
            background-color: #ffc107;
            color: #212529;
            border: none;
        }

        .btn-warning.btn-action:hover {
            background-color: #e0a800;
            color: #fff;
        }

        .btn-danger.btn-action {
            background-color: #dc3545;
            border: none;
            color: white;
        }

        .btn-danger.btn-action:hover {
            background-color: #b02a37;
        }

        /* Modal header */
        .modal-header {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>

<body>

    <!-- Thin top navbar -->
    <nav class="top-navbar"></nav>

    <!-- Main navbar -->
    <nav class="navbar navbar-expand-lg navbar-primary">
        <div class="container d-flex align-items-center">
            <span class="navbar-brand">🧑‍⚖️ Judge Management Panel</span>
            <a href="../index.php" class="btn btn-back btn-sm ms-auto">
                Back <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </nav>

    <div class="container">
        <!-- Button trigger modal -->
        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJudgeModal">Add Judge</button>
        </div>

        <!-- Judges Table -->
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Judge Code</th>
                    <th>Display Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM judges ORDER BY id ASC");
                if ($result->num_rows > 0) {
                    $index = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$index}</td>
                            <td>" . htmlspecialchars($row['judge_code']) . "</td>
                            <td>" . htmlspecialchars($row['display_name']) . "</td>
                            <td>
                                <a href='edit_judge.php?id={$row['id']}' class='btn btn-warning btn-sm btn-action' title='Edit'>
                                    <i class='bi bi-pencil-square'></i>
                                </a>
                                <a href='delete_judge.php?id={$row['id']}' class='btn btn-danger btn-sm btn-action' onclick='return confirm(\"Are you sure you want to delete this judge?\")' title='Delete'>
                                    <i class='bi bi-trash'></i>
                                </a>
                            </td>
                        </tr>";
                        $index++;
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>No judges found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Add Judge Modal -->
    <div class="modal fade" id="addJudgeModal" tabindex="-1" aria-labelledby="addJudgeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addJudgeModalLabel">Add New Judge</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="judge_code" class="form-label">Judge Code</label>
                        <input type="text" name="judge_code" id="judge_code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="display_name" class="form-label">Display Name</label>
                        <input type="text" name="display_name" id="display_name" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_judge" class="btn btn-primary">Add Judge</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <?php
    if (isset($_POST['add_judge'])) {
        $judge_code = trim($_POST['judge_code']);
        $display_name = trim($_POST['display_name']);

        if (!empty($judge_code) && !empty($display_name)) {
            $stmt = $conn->prepare("INSERT INTO judges (judge_code, display_name) VALUES (?, ?)");
            $stmt->bind_param("ss", $judge_code, $display_name);
            if ($stmt->execute()) {
                echo "<script>window.location.href='add_judge.php';</script>";
            } else {
                echo "<script>alert('Error: " . $stmt->error . "');</script>";
            }
        } else {
            echo "<script>alert('All fields are required.');</script>";
        }
    }
    ?>

</body>

</html>