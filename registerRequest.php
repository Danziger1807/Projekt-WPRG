<?php
session_start();
require_once "connect.php";


//checking connection with SZUFLANDIA:(
$connection = @new mysqli($host, $db_user, $db_password, $db_name);
if ($connection->connect_errno != 0) {
    $_SESSION['error'] = "Błąd połączenia z bazą danych!";
    header('Location: register.php');
    exit();
}


//user's form data 
$login = trim($_POST['login']);
$email = trim($_POST['email']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$regulamin = isset($_POST['regulamin']);


if (!$regulamin) {
    $_SESSION['error'] = "Musisz zaakceptować regulamin.";
    header('Location: register.php');
    exit();
}

if (strlen($login) < 3 || strlen($login) > 50) {
    $_SESSION['error'] = "Login musi mieć od 3 do 50 znaków.";
    header('Location: register.php');
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
    $_SESSION['error'] = "Nieprawidłowy adres e-mail.";
    header('Location: register.php');
    exit();
}

if (strlen($password) < 6 || strlen($password) > 255) {
    $_SESSION['error'] = "Hasło musi mieć od 6 do 255 znaków.";
    header('Location: register.php');
    exit();
}

if ($password !== $confirm_password) {
    $_SESSION['error'] = "Hasła się nie zgadzają.";
    header('Location: register.php');
    exit();
}

// No sql injection
$login_safe = $connection->real_escape_string($login);
$email_safe = $connection->real_escape_string($email);


// checking if user already exist
$query = "SELECT * FROM users_data WHERE userName='$login_safe' OR email='$email_safe'";
$result = $connection->query($query);

if ($result->num_rows > 0) {
    $_SESSION['error'] = "Użytkownik o podanym loginie lub adresie e-mail już istnieje.";
    $result->free_result();
    $connection->close();
    header('Location: register.php');
    exit();
}

// adding users and hashing password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$query_insert = sprintf(
    "INSERT INTO users_data (userName, email, pass) VALUES ('%s', '%s', '%s')",
    $login_safe,
    $email_safe,
    $connection->real_escape_string($hashed_password)
);

if ($connection->query($query_insert)) {
    $_SESSION['logged'] = true;
    $_SESSION['user'] = $login;
    $_SESSION['id'] = $connection->insert_id; 
    $_SESSION['success'] = "Rejestracja zakończona sukcesem.";
    header('Location: dashBoard.php');
} else {
    $_SESSION['error'] = "Wystąpił błąd podczas rejestracji.";
    header('Location: register.php');
}

$connection->close();
?>
