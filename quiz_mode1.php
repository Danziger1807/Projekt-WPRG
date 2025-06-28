<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once "connect.php";

if (!isset($_SESSION['logged']) || !isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['id'];
$setId = $_GET['set_id'] ?? null;

if (!$setId) {
    die("Nie wybrano zestawu.");
}

$connection = new mysqli($host, $db_user, $db_password, $db_name);
if ($connection->connect_errno) {
    die("Błąd połączenia z bazą: " . $connection->connect_error);
}


$stmtCheck = $connection->prepare("SELECT user_id, title FROM flashcards WHERE id = ?");
$stmtCheck->bind_param("i", $setId);
$stmtCheck->execute();
$resultCheck = $stmtCheck->get_result();

if ($resultCheck->num_rows === 0) {
    die("Zestaw nie istnieje.");
}

$rowCheck = $resultCheck->fetch_assoc();
if ($rowCheck['user_id'] != $userId) {
    die("Brak dostępu do tego zestawu.");
}
$setTitle = $rowCheck['title'];
$stmtCheck->close();


$stmt = $connection->prepare("SELECT question, answer FROM flashcard_questions WHERE flashcard_id = ?");
$stmt->bind_param("i", $setId);
$stmt->execute();
$result = $stmt->get_result();

$flashcards = [];
while ($row = $result->fetch_assoc()) {
    $flashcards[] = $row;
}
$stmt->close();
$connection->close();

$currentIndex = isset($_POST['currentIndex']) ? (int)$_POST['currentIndex'] : 0;
$showAnswer = isset($_POST['showAnswer']);
$maxIndex = count($flashcards) - 1;

if ($currentIndex > $maxIndex) {
    $currentIndex = $maxIndex;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Tryb przeglądania fiszek - <?= htmlspecialchars($setTitle) ?></title>
     <link rel="stylesheet" href="css/mode_style.css">
    
</head>
<body>
    <div class="card-container">
        <h2><?= htmlspecialchars($setTitle) ?></h2>

        <?php if (count($flashcards) === 0): ?>
            <p>Brak fiszek w tym zestawie.</p>

        <?php elseif ($currentIndex > $maxIndex): ?>
            <p>To już wszystkie fiszki!</p>

        <?php else: ?>
            <div class="card">
                <?php if ($showAnswer): ?>
                    <strong>Odpowiedź:</strong><br>
                    <?= nl2br(htmlspecialchars($flashcards[$currentIndex]['answer'])) ?>
                <?php else: ?>
                    <strong>Pytanie:</strong><br>
                    <?= nl2br(htmlspecialchars($flashcards[$currentIndex]['question'])) ?>
                <?php endif; ?>
            </div>

            <form method="post">
                <?php if (!$showAnswer): ?>
                    <input type="hidden" name="currentIndex" value="<?= $currentIndex ?>">
                    <button type="submit" name="showAnswer" class="btn">Pokaż odpowiedź</button>
                <?php else: ?>
                    <input type="hidden" name="currentIndex" value="<?= $currentIndex + 1 ?>">
                    <button type="submit" class="btn">Następna fiszka</button>
                <?php endif; ?>
            </form>
        <?php endif; ?>

        
        <a href="dashBoard.php" class="btn-link">← Powrót do dashboardu</a>
    </div>
</body>
</html>
