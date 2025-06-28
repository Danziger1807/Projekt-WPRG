<?php
session_start();
if((isset($_SESSION['logged']))&&($_SESSION['logged']==true))
{
    header('Location:dashBoard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Zaloguj się — ThinkCherry</title>
  <link rel="stylesheet" href="css/logsystem.css" />
</head>
<body>
  <div class="form-container">
    <h2>Zaloguj się do ThinkCherry</h2>
    <form method="POST" action="logRequest.php">
      <input type="text" name="login" placeholder="Login" required />
      <input type="password" name="password" placeholder="Hasło" required />
      <button type="submit" class="btn btn-red">Zaloguj się</button>
    </form>
     <?php 
        if(isset($_SESSION['error'])){
            '<br>';
            echo $_SESSION['error'];
            '<br>';
        }

    ?>

    <p>Nie masz konta? <a href="register.php">Zarejestruj się za darmo</a></p>
  </div>
</body>
</html>
