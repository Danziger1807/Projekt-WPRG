<?php
session_start();
require_once "connect.php";

if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['id'];

$connection = new mysqli($host, $db_user, $db_password, $db_name);
if ($connection->connect_errno) {
    die("Błąd połączenia z bazą danych.");
}

$query = $connection->prepare("SELECT id, title FROM flashcards WHERE user_id = ?");
$query->bind_param("i", $userId);
$query->execute();
$result = $query->get_result();

$sets = [];
while ($row = $result->fetch_assoc()) {
    $sets[] = $row;
}
$query->close();
$connection->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Wybór zestawu – ThinkCherry</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f9f9f9;
            padding: 40px;
            text-align: center;
        }

        h1 {
            color: #ea3b3b;
        }

        .set-list {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            margin-top: 30px;
        }

        .set-item {
            background: #ffeaea;
            padding: 16px 24px;
            border-radius: 10px;
            width: 300px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .set-item a {
            text-decoration: none;
            background: #ea3b3b;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
        }

        .back {
            margin-top: 30px;
            display: inline-block;
            text-decoration: none;
            border: 2px solid #ea3b3b;
            color: #ea3b3b;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Wybierz zestaw fiszek do quizu</h1>

    <div class="set-list">
        <?php if (empty($sets)): ?>
            <p>Nie masz jeszcze żadnych zestawów.</p>
        <?php else: ?>
            <?php foreach ($sets as $set): ?>
                <div class="set-item">
                    <?= htmlspecialchars($set['title']) ?>
                    <a href="quiz_mode1.php?set_id=<?= $set['id'] ?>">Start</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <a href="dashBoard.php" class="back">⟵ Powrót do dashboardu</a>
</body>
</html>
