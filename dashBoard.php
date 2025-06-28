<?php
session_start();
require_once "connect.php"; 

if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header('Location: login.php');
    exit();
}

$connection = new mysqli($host, $db_user, $db_password, $db_name);
if ($connection->connect_errno) {
    die("Błąd połączenia z bazą: " . $connection->connect_error);
}

$userId = $_SESSION['id'];
$userName = $_SESSION['user'];


$sql = "SELECT id, title, question_count FROM flashcards WHERE user_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$userFlashcards = [];
while ($row = $result->fetch_assoc()) {
    $userFlashcards[] = $row;
}

$stmt->close();
$connection->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ThinkCherry Dashboard</title>
   <link rel="stylesheet" href="css/DashBoard_style.css">
</head>
<body>
  <header>
    <div class="logo">
      <img src="https://cdn-icons-png.flaticon.com/128/7254/7254245.png" alt="Cherry" /> ThinkCherry
    </div>
    <nav>
      <a href="#">Quizy</a>
      <a href="#">Materiały</a>
      <a href="#">Profil</a>
      <a href="logout.php">Wyloguj się</a>
    </nav>
    <div class="profile-icon">
      <img src="https://cdn-icons-png.flaticon.com/512/1077/1077063.png" alt="Profil" />
    </div>
  </header>

  <div class="top-buttons">
    <button class="flashcard-btn" onclick="location.href='flashcards.php'">Flashcards</button>
    <button class="quiz-btn" onclick="location.href='choose_set.php'">Rozpocznij quiz</button>



  </div>

  <section class="section">
    <h2>Twoje Quizy</h2>
    <div class="quiz-grid">
      <?php if (count($userFlashcards) > 0): ?>
        <?php foreach ($userFlashcards as $flashcard): ?>
          <div class="quiz-card" onclick="location.href='flashcard_detail.php?id=<?= $flashcard['id'] ?>'">
            <?= htmlspecialchars($flashcard['title']) ?>
            <small><?= (int)$flashcard['question_count'] ?> pytań</small>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>Nie masz jeszcze żadnych quizów. Utwórz swój pierwszy zestaw!</p>
      <?php endif; ?>

      <div class="create-card" onclick="location.href='create_flashcard.php'">
        <span>💬</span>
        Utwórz zestaw
      </div>
    </div>
  </section>
</body>
</html>
