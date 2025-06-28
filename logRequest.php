<?php
session_start();

if (!isset($_POST['login']) || !isset($_POST['password'])) {
    header('Location: login.php');
    exit();
}

require_once "connect.php";

$connection = @new mysqli($host, $db_user, $db_password, $db_name);

if ($connection->connect_errno != 0) {
    echo "Error: " . $connection->connect_errno;
    exit();
}

$login = trim($_POST['login']);
$password = $_POST['password'];

$login_safe = $connection->real_escape_string($login);

$query = sprintf("SELECT * FROM users_data WHERE userName='%s'", $login_safe);

if ($result = $connection->query($query)) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

       
        if (password_verify($password, $row['pass'])) {
            $_SESSION['logged'] = true;
            $_SESSION['user'] = $row['userName'];
            $_SESSION['id'] = $row['id'];
            unset($_SESSION['error']);
            $result->free_result();
            header('Location: dashBoard.php');
            exit();
        } else {
            $_SESSION['error'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
            header('Location: login.php');
            exit();
        }
    } else {
        $_SESSION['error'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
        header('Location: login.php');
        exit();
    }
}

$connection->close();
?>
