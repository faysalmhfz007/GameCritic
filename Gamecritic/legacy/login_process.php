<?php
session_start();
require_once "db/db.php";


$email = trim($_POST['email']);
$password = trim($_POST['password']);


$sql = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
$result = $conn->query($sql);


if (!$result) {
    die("Query error: " . $conn->error);
}

if ($result->num_rows == 1) {
    $_SESSION['user_email'] = $email;


    if ($email === "admin@gamecritic.com") {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: user_home.php");
    }

    exit();
} else {

    header("Location: login.php?error=invalid_credentials");
    exit();
}




