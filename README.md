Instructions on How to Run the Project Locally (Using XAMPP)
Set up XAMPP
Install and launch XAMPP, make sure Apache and MySQL services are running.

Copy Project Files
place the project folder (e.g., judging-system) inside the htdocs directory:

C:\xampp\htdocs\judging-system
Create Database and Import Schema

Open phpMyAdmin via localhost/phpmyadmin

Created a new database called judging_system

Import the SQL schema to create users, judges, and scores tables

Run the Project
Access the project in the browser via:

http://localhost/judging-system/
Admin Panel: /admin/

Judge Portal: /judge/

Scoreboard: /public/

SQL Schema to Create Tables
create three relational tables:

-- Users (Participants)
CREATE TABLE users (
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100) NOT NULL
);

-- Judges
CREATE TABLE judges (
id INT AUTO_INCREMENT PRIMARY KEY,
judge_id VARCHAR(50) NOT NULL UNIQUE,
name VARCHAR(100) NOT NULL
);

-- Scores (Linking judges and users)
CREATE TABLE scores (
id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT,
judge_id INT,
score INT CHECK (score >= 1 AND score <= 100),
FOREIGN KEY (user_id) REFERENCES users(id),
FOREIGN KEY (judge_id) REFERENCES judges(id)
);

Admin Panel for Judge Management
I created an admin panel (no login required for this version) where I can:

Add judges by entering their name and a unique judge CODE.

View all judges that have been added to the system.

The data is stored in a judges table, and I've kept the layout simple for easy navigation. If I had more time, I would definitely add authentication to protect the admin section.

Judge Portal & Scoring
I built a judge scoring interface where each judge logs in via the Judge CODE and can:

View a list of all users (participants).

Select a user and assign them a numeric score between 1 and 100.

The judges and users are linked in a scores table that records each judge’s score for each participant. This allows multiple judges to score the same user independently.

Public Scoreboard Display
I created a public scoreboard that:

Shows a list of all users with their total accumulated points.

Automatically sorts the list in descending order based on total points.

Highlights users visually depending on their scores using CSS.

The scoreboard auto-refreshes every few seconds using JavaScript and a simple <meta> tag inside the <head> section of my HTML file to automatically refresh the entire page at set intervals.

Assumptions Made
Users (participants) are pre-registered manually in the users table just for this demo.

No authentication was required, so I left login functionality out to keep it simple and focused on scoring logic.

Judges can score any user without restrictions or login (in a real app, they’d be authenticated and limited to one score per user).

Design Decisions I Made
No login system was implemented for now to keep the project lightweight and focused on functionality. In a production version, I would add secure authentication for both admin and judges.

I used relational tables with foreign keys (e.g., scores.user_id and scores.judge_id) to link users and judges. This ensures scalability and data integrity.

I separated files for clarity: admin, judge, and public sections for clean structure and easy navigation.

I used basic JavaScript (setInterval with fetch) to auto-refresh the scoreboard.

If I Had More Time
I would implement login systems for both admin and judges using sessions.

Add input validation and more advanced error handling.

Allow exporting reports (CSV/PDF) from the scoreboard.

Include AJAX for smoother, live score updates without page refresh.
