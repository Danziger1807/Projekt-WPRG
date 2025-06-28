<?php
session_start();
if ((isset($_SESSION['logged'])) && ($_SESSION['logged'] == true)) {
    header('Location:dashBoard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Zarejestruj się — ThinkCherry</title>
  <style>
    body {
      background: #ffeaea;
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .form-container {
      background: #fff;
      padding: 40px;
      border-radius: 16px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      animation: fadeInUp 1s ease-out;
      max-width: 400px;
      width: 100%;
      text-align: center;
    }

    .form-container h2 {
      color: #ea3b3b;
      margin-bottom: 24px;
    }

    .form-container form {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .form-container input {
      padding: 14px;
      border: 1px solid #ddd;
      border-radius: 8px;
      font-size: 16px;
    }

    .form-container p {
      margin-top: 16px;
      font-size: 14px;
    }

    .form-container a {
      color: #ea3b3b;
      text-decoration: none;
      font-weight: bold;
    }

    .btn {
      padding: 14px;
      font-size: 16px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-weight: bold;
      transition: transform 0.2s;
    }

    .btn-red {
      background: #ea3b3b;
      color: white;
    }

    .btn:hover {
      transform: translateY(-3px);
    }

    @keyframes fadeInUp {
      from { transform: translateY(30px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    .checkbox-label {
      display: block;
      margin-top: 12px;
      font-size: 14px;
      color: #333;
    }

    .checkbox-label input {
      margin-right: 6px;
    }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      padding-top: 100px;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.6);
    }

    .modal-content {
      background-color: #fff;
      margin: auto;
      padding: 20px;
      border-radius: 12px;
      width: 80%;
      max-width: 600px;
      position: relative;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    }

    .close {
      color: #aaa;
      position: absolute;
      right: 20px;
      top: 10px;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }

    .close:hover {
      color: #ea3b3b;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Załóż konto ThinkCherry</h2>
    <form method="POST" action="registerRequest.php" id="register-form">
      <input type="text" name="login" placeholder="Login" required />
      <input type="email" name="email" placeholder="Adres e-mail" required />
      <input type="password" name="password" placeholder="Hasło" required />
      <input type="password" name="confirm_password" placeholder="Powtórz hasło" required />
      
      <label class="checkbox-label">
        <input type="checkbox" name="regulamin" required />
        Akceptuję <a href="#" id="show-terms">regulamin</a>
      </label>

      <div id="recaptcha-box" style="display: none; margin-top: 16px;">
        <div class="g-recaptcha" data-sitekey="6Leg1FkrAAAAABZ-y2chfQZ6J1Gyc7IHz1essUvV"></div>
      </div>

      <button type="submit" class="btn btn-red" id="register-btn">Zarejestruj się</button>
    </form>

    <?php 
      if(isset($_SESSION['error'])){
          echo '<p class="error-message">'.$_SESSION['error'].'</p>';
          unset($_SESSION['error']);
      }
    ?>

    <p>Masz już konto? <a href="login.php">Zaloguj się</a></p>
  </div>

 
  <div id="modal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <h2>Regulamin serwisu ThinkCherry</h2>
      <p>1. Korzystając z serwisu, zgadzasz się na przetwarzanie danych osobowych w celach związanych z funkcjonowaniem platformy.</p>
      <p>2. Użytkownik zobowiązuje się do niepublikowania treści niezgodnych z prawem.</p>
      <p>3. Administrator zastrzega sobie prawo do usunięcia konta bez podania przyczyny.</p>
      <p>4. Użytkownik akceptuje możliwość wysyłania komunikatów systemowych.</p>
      <p>5. Dane logowania muszą być chronione przez użytkownika.</p>
    </div>
  </div>

 
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  <script>
    const registerBtn = document.getElementById('register-btn');
    const recaptchaBox = document.getElementById('recaptcha-box');

    registerBtn.addEventListener('click', function (e) {
      if (recaptchaBox.style.display === 'none') {
        e.preventDefault();
        recaptchaBox.style.display = 'block';
      }
    });

    document.getElementById("show-terms").addEventListener("click", function(e) {
      e.preventDefault();
      document.getElementById("modal").style.display = "block";
    });

    document.querySelector(".close").addEventListener("click", function() {
      document.getElementById("modal").style.display = "none";
    });

    window.addEventListener("click", function(e) {
      if (e.target == document.getElementById("modal")) {
        document.getElementById("modal").style.display = "none";
      }
    });
  </script>
</body>
</html>
