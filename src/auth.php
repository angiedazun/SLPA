<?php
session_start();
require '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = $_POST['userID'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM System_Users WHERE UserID = ?");
    $stmt->execute([$userID]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['Password'])) {
        $_SESSION['user'] = $userID;
        header('Location: dashboard.php');
        exit;
    } else {
        echo "<script>alert('Invalid User ID or Password'); window.location='../public/index.php';</script>";
        exit;
    }
} else {
    header('Location: ../public/index.php');
    exit;
}
