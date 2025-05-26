<?php
session_start();
require_once '../db_connect.php';

// Fetch the total number of judges for average calculation
$judgeCountResult = $conn->query("SELECT COUNT(*) AS judge_count FROM judges");
$judgeCountRow = $judgeCountResult->fetch_assoc();
$judgeCount = max(1, (int)$judgeCountRow['judge_count']); // avoid division by zero

// Fetch users with average score
$sql = "
    SELECT 
        users.id,
        users.full_name,
        COALESCE(SUM(scores.score), 0) / $judgeCount AS avg_score
    FROM users
    LEFT JOIN scores ON users.id = scores.user_id
    GROUP BY users.id, users.full_name
    ORDER BY avg_score DESC, users.full_name ASC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="refresh" content="30"> <!-- Auto-refresh every 30 seconds -->
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Public Scoreboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

    <style>
        body {
            background: linear-gradient(135deg, #e0f7fa, #fffde7);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 2rem;
            min-height: 100vh;
        }

        h1 {
            color: #0277bd;
            /* Dark blue */
            margin-bottom: 2rem;
            text-align: center;
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }

        .btn-back {
            display: block;
            max-width: 200px;
            margin: 0 auto 2rem auto;
            background-color: #ffeb3b;
            /* bright yellow */
            color: #0277bd;
            /* dark blue */
            font-weight: 600;
            border-radius: 30px;
            box-shadow: 0 4px 6px rgba(255, 235, 59, 0.4);
            transition: background-color 0.3s ease, color 0.3s ease;
            border: none;
        }

        .btn-back:hover {
            background-color: #fdd835;
            color: #01579b;
        }

        table {
            width: 100%;
            background: #ffffffcc;
            /* white with slight transparency */
            border-radius: 12px;
            box-shadow: 0 8px 20px rgb(0 123 255 / 0.2);
            border-collapse: separate;
            border-spacing: 0;
            overflow: hidden;
        }

        thead th {
            background-color: #0288d1;
            /* strong blue */
            color: white;
            text-align: center;
            font-weight: 700;
            padding: 1rem 1.25rem;
            border-bottom: 3px solid #01579b;
        }

        tbody td {
            vertical-align: middle !important;
            text-align: center;
            font-weight: 500;
            font-size: 1rem;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e3f2fd;
        }

        tbody tr:hover {
            background-color: #bbdefb88;
            cursor: default;
        }

        /* Highlight rows with avg_score >= 8 */
        tbody tr.highlight {
            background-color: #d0f0c0;
            /* soft green */
            font-weight: 600;
            color: #2e7d32;
        }

        .rank {
            font-weight: 700;
            color: #01579b;
        }

        .score {
            font-weight: 700;
            color: #0288d1;
        }
    </style>
</head>

<body>

    <h1>Public Scoreboard</h1>

    <button class="btn btn-back" onclick="location.href='../index.php'">&larr; Back to Home Page</button>

    <div class="container">
        <table class="table align-middle">
            <thead>
                <tr>
                    <th style="width: 10%;">Position</th>
                    <th>Participant Name</th>
                    <th style="width: 20%;">Average Score</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $position = 1;
                while ($row = $result->fetch_assoc()):
                    // Highlight if avg_score >= 8
                    $highlightClass = ($row['avg_score'] >= 8) ? 'highlight' : '';
                ?>
                    <tr class="<?php echo $highlightClass; ?>">
                        <td class="rank"><?php echo $position++; ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td class="score"><?php echo number_format($row['avg_score'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>