<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: ../src/dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Toner System</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style.css" />
  <style>
    #bg-video {
      position: fixed;
      right: 0;
      bottom: 0;
      min-width: 100vw;
      min-height: 100vh;
      width: auto;
      height: auto;
      z-index: -1;
      object-fit: cover;
      background: #000;
    }
    .login-container {
      position: relative;
      z-index: 1;
      background: rgba(255,255,255,0.85);
      border-radius: 10px;
      padding: 2rem;
      margin-top: 5vh;
    }
    body {
      min-height: 100vh;
      overflow: hidden;
    }
  </style>
</head>
<body>
  <video autoplay muted loop id="bg-video">
    <source src="img/Login background.mp4" type="video/mp4">
    Your browser does not support the video tag.
  </video>
  <div class="login-container shadow-sm">
    <div class="text-center mb-3">
      <img src="img/logo new.png" alt="Toner System Logo" style="max-width: 120px; width: 100%; height: auto;">
    </div>
    <h2 class="text-center">Toner System Login</h2>
    <form method="POST" action="../src/auth.php" class="needs-validation" novalidate>
      <div class="mb-3">
        <label for="userID" class="form-label">User ID</label>
        <input type="text" class="form-control" id="userID" name="userID" required />
        <div class="invalid-feedback">Please enter your User ID.</div>
      </div>
      <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required />
        <div class="invalid-feedback">Please enter your password.</div>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- Custom JS -->
  <script src="js/script.js"></script>
</body>
</html>
