<?php
echo '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Judging Management System</title>
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #e0f0ff, #fff9db); /* soft blue to yellow */
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            display: flex;
            max-width: 900px;
            width: 90%;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            min-height: 400px;
        }

        .left-side {
            flex: 1;
            background: #0d6efd; /* Bootstrap primary blue */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0; /* removed padding for full image coverage */
            min-height: 100%;
            overflow: hidden;
            border-top-left-radius: 16px;
            border-bottom-left-radius: 16px;
        }

        .left-side img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-top-left-radius: 16px;
            border-bottom-left-radius: 16px;
            box-shadow: 0 8px 16px rgba(255, 255, 255, 0.3);
        }

        .right-side {
            flex: 1;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        h1 {
            color: #004aad; /* dark blue */
            margin-bottom: 2rem;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.15);
            font-weight: 700;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        li {
            margin-bottom: 1.5rem;
        }

        a {
            display: inline-block;
            padding: 12px 28px;
            font-size: 18px;
            font-weight: 600;
            color: white;
            border-radius: 10px;
            box-shadow: 0 5px 12px rgba(0,0,0,0.15);
            text-decoration: none;
            transition: transform 0.25s, box-shadow 0.25s;
        }

        a.blue {
            background-color: #007bff;
        }
        a.blue:hover {
            background-color: #0056b3;
            box-shadow: 0 8px 20px rgba(0, 123, 255, 0.6);
            transform: scale(1.05);
        }

        a.yellow {
            background-color: #ffc107;
            color: #333;
        }
        a.yellow:hover {
            background-color: #e0a800;
            box-shadow: 0 8px 20px rgba(255, 193, 7, 0.6);
            transform: scale(1.05);
            color: white;
        }

        a.green {
            background-color: #28a745;
        }
        a.green:hover {
            background-color: #1e7e34;
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.6);
            transform: scale(1.05);
        }

        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInUp 0.6s forwards;
        }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                min-height: auto;
            }

            .left-side, .right-side {
                flex: none;
                width: 100%;
                padding: 1.5rem 1rem;
            }

            .left-side img {
                max-height: 250px;
                border-radius: 12px;
                width: auto;
                height: auto;
            }

            ul {
                text-align: center;
            }

            a {
                width: 100%;
                display: block;
            }

            li {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-side fade-in">
            <img src="images/judging_image.jpg" alt="Judging Management" />
        </div>
        <div class="right-side">
            <h1 class="fade-in">Judging Management System</h1>
            <ul>
                <li class="fade-in" style="animation-delay: 0.2s;"><a class="blue" href="admin/add_judge.php">Admin Panel – Add Judge</a></li>
                <li class="fade-in" style="animation-delay: 0.4s;"><a class="yellow" href="judge/score_user.php?judge_id=1">Judge Panel – Score Users</a></li>
                <li class="fade-in" style="animation-delay: 0.6s;"><a class="green" href="public/scoreboard.php">Public Scoreboard</a></li>
            </ul>
        </div>
    </div>

    <script>
        // Add delay to fade-in effect for staggered appearance
        const fadeElements = document.querySelectorAll(".fade-in");
        fadeElements.forEach((el, index) => {
            el.style.animationDelay = `${index * 0.2}s`;
        });
    </script>
</body>
</html>
';
