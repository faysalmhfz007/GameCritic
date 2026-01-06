<?php
session_start();
require __DIR__ . '/db/db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';


    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Please fill in all fields.";
        header("Location: admin_login.php");
        exit();
    }


    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        if ($password === $admin['password']) {

            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];


            header("Location: admin_dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Incorrect password.";
            header("Location: admin_login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Admin not found.";
        header("Location: admin_login.php");
        exit();
    }
} else {
    header("Location: admin_login.php");
    exit();
}
