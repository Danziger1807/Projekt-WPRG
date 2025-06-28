<?php
session_start();
require_once "connect.php";

$userId = $_SESSION['id'];
if (!isset($_SESSION['id'])) {
    die("🛑 Błąd: Sesja nie zawiera ID użytkownika. Sprawdź logowanie.<br><pre>" . print_r($_SESSION, true) . "</pre>");
}

$userId = $_SESSION['id'];


if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header('Location: login.php');
    exit();
}

$connection = new mysqli($host, $db_user, $db_password, $db_name);
if ($connection->connect_errno) {
    die("Błąd połączenia z bazą: " . $connection->connect_error);
}

$userId = $_SESSION['id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');

    if (empty($title)) {
        $error = "Tytuł zestawu nie może być pusty.";
    } else {
        $stmt = $connection->prepare("INSERT INTO flashcards (user_id, title, question_count) VALUES (?, ?, 0)");
        $stmt->bind_param("is", $userId, $title);

        if ($stmt->execute()) {
            $success = "Zestaw został utworzony!";
            header('Location: dashBoard.php');
            exit();
        } else {
            $error = "Błąd podczas tworzenia zestawu: " . $stmt->error;
        }

        $stmt->close();
    }
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Utwórz nowy zestaw - ThinkCherry</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f9f9f9;
      color: #1a1a1a;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .container {
      background-color: white;
      padding: 24px 32px;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      width: 320px;
      text-align: center;
    }
    h1 {
      margin-bottom: 16px;
      color: #ea3b3b;
    }
    input[type="text"] {
      width: 100%;
      padding: 12px;
      border: 2px solid #eee;
      border-radius: 8px;
      font-size: 16px;
      margin-bottom: 16px;
      box-sizing: border-box;
    }
    input[type="submit"] {
      background-color: #ea3b3b;
      color: white;
      border: none;
      padding: 12px 20px;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      width: 100%;
      font-size: 16px;
      transition: background-color 0.3s ease;
    }
    input[type="submit"]:hover {
      background-color: #cc3232;
    }
    .error {
      color: #ea3b3b;
      margin-bottom: 12px;
    }
    .success {
      color: green;
      margin-bottom: 12px;
    }
    a {
      display: inline-block;
      margin-top: 16px;
      color: #ea3b3b;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Utwórz nowy zestaw</h1>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post" action="">
      <input type="text" name="title" placeholder="Tytuł zestawu" required />
      <input type="submit" value="Utwórz" />
    </form>

    <a href="dashBoard.php">Powrót do dashboardu</a>
  </div>
</body>
</html>
