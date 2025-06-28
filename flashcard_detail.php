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
$flashcardId = $_GET['id'] ?? null;
if (!$flashcardId || !is_numeric($flashcardId)) {
    die("Nieprawidłowy identyfikator zestawu.");
}

// Pobierz zestaw
$stmt = $connection->prepare("SELECT title FROM flashcards WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $flashcardId, $userId);
$stmt->execute();
$result = $stmt->get_result();
$flashcard = $result->fetch_assoc();
if (!$flashcard) {
    die("Zestaw nie istnieje lub nie masz do niego dostępu.");
}

// Obsługa dodania nowego pytania
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        if ($question === '' || $answer === '') {
            $error = "Pytanie i odpowiedź nie mogą być puste.";
        } else {
            $stmtInsert = $connection->prepare("INSERT INTO flashcard_questions (flashcard_id, question, answer) VALUES (?, ?, ?)");
            $stmtInsert->bind_param("iss", $flashcardId, $question, $answer);
            if ($stmtInsert->execute()) {
                $stmtUpdate = $connection->prepare("UPDATE flashcards SET question_count = question_count + 1 WHERE id = ?");
                $stmtUpdate->bind_param("i", $flashcardId);
                $stmtUpdate->execute();
                $stmtUpdate->close();
                header("Location: flashcard_detail.php?id=$flashcardId");
                exit();
            } else {
                $error = "Błąd podczas dodawania pytania: " . $stmtInsert->error;
            }
            $stmtInsert->close();
        }
    }

    // Obsługa edycji pytania
    if (isset($_POST['edit']) && isset($_POST['edit_id'])) {
        $editId = (int)$_POST['edit_id'];
        $newQuestion = trim($_POST['new_question'] ?? '');
        $newAnswer = trim($_POST['new_answer'] ?? '');
        if ($newQuestion !== '' && $newAnswer !== '') {
            $stmtEdit = $connection->prepare("UPDATE flashcard_questions SET question = ?, answer = ? WHERE id = ? AND flashcard_id = ?");
            $stmtEdit->bind_param("ssii", $newQuestion, $newAnswer, $editId, $flashcardId);
            $stmtEdit->execute();
            $stmtEdit->close();
            header("Location: flashcard_detail.php?id=$flashcardId");
            exit();
        } else {
            $error = "Pola edycji nie mogą być puste.";
        }
    }

    // Obsługa usuwania pytania
    if (isset($_POST['delete']) && isset($_POST['delete_id'])) {
        $deleteId = (int)$_POST['delete_id'];
        $stmtDel = $connection->prepare("DELETE FROM flashcard_questions WHERE id = ? AND flashcard_id = ?");
        $stmtDel->bind_param("ii", $deleteId, $flashcardId);
        if ($stmtDel->execute()) {
            $stmtUpdate = $connection->prepare("UPDATE flashcards SET question_count = question_count - 1 WHERE id = ?");
            $stmtUpdate->bind_param("i", $flashcardId);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        }
        $stmtDel->close();
        header("Location: flashcard_detail.php?id=$flashcardId");
        exit();
    }
}

// Pobierz pytania
$stmt = $connection->prepare("SELECT id, question, answer FROM flashcard_questions WHERE flashcard_id = ?");
$stmt->bind_param("i", $flashcardId);
$stmt->execute();
$result = $stmt->get_result();
$questions = [];
while ($row = $result->fetch_assoc()) {
    $questions[] = $row;
}
$stmt->close();
$connection->close();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($flashcard['title']) ?> - ThinkCherry</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background-color: #f9f9f9; max-width: 800px; margin: 40px auto; padding: 0 16px; }
    h1 { color: #ea3b3b; margin-bottom: 24px; }
    .question-item, form { background: white; padding: 16px; border-radius: 8px; margin-bottom: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .question-item strong { display: block; margin-bottom: 6px; }
    textarea, input[type="text"] { width: 100%; padding: 10px; border: 2px solid #eee; border-radius: 8px; font-size: 16px; margin-bottom: 12px; }
    input[type="submit"], button { background-color: #ea3b3b; color: white; border: none; padding: 10px 16px; border-radius: 8px; font-weight: bold; cursor: pointer; font-size: 15px; }
    input[type="submit"]:hover, button:hover { background-color: #cc3232; }
    .error { color: #ea3b3b; margin-bottom: 16px; }
    .edit-form { margin-top: 8px; }
    .actions { margin-top: 8px; }
  </style>
</head>
<body>
  <h1><?= htmlspecialchars($flashcard['title']) ?></h1>

  <section class="question-list">
    <?php foreach ($questions as $q): ?>
      <div class="question-item">
        <strong>Pytanie:</strong> <?= htmlspecialchars($q['question']) ?>
        <strong>Odpowiedź:</strong> <?= htmlspecialchars($q['answer']) ?>
        <div class="actions">
          <form method="post" style="display:inline;">
            <input type="hidden" name="delete_id" value="<?= $q['id'] ?>">
            <input type="submit" name="delete" value="Usuń">
          </form>
        </div>
        <form method="post" class="edit-form">
          <input type="hidden" name="edit_id" value="<?= $q['id'] ?>">
          <label>Edytuj pytanie:</label>
          <textarea name="new_question" rows="2" required><?= htmlspecialchars($q['question']) ?></textarea>
          <label>Edytuj odpowiedź:</label>
          <textarea name="new_answer" rows="2" required><?= htmlspecialchars($q['answer']) ?></textarea>
          <input type="submit" name="edit" value="Zapisz zmiany">
        </form>
      </div>
    <?php endforeach; ?>
    <?php if (empty($questions)): ?>
      <p>Brak pytań.</p>
    <?php endif; ?>
  </section>

  <section class="add-question">
    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
      <label>Dodaj pytanie:</label>
      <input type="text" name="question" placeholder="Pytanie">
      <textarea name="answer" placeholder="Odpowiedź"></textarea>
      <input type="submit" name="add" value="Dodaj">
    </form>
  </section>
   <a href="dashBoard.php">« Powrót do dashboardu</a>
</body>
</html>
