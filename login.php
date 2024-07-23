<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hardcoded credentials for demonstration
    $valid_username = 'demouser';
    $valid_password = 'ThisIsForWPClass';

    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['username'] = $username;
        header('Location: search.html');
        exit();
    } else {
        echo 'Invalid credentials. Please try again.';
    }
}
?>
