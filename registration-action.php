<?php
session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!isset($_POST['email'])) {
        $_SESSION['feedback'] = "Email is required!";
        header('Location: registration.php');
        die;
    }
    if(!isset($_POST['password'])) {
        $_SESSION['feedback'] = "Password is required!";
        header('Location: registration.php');
        die;
    }
    if(!isset($_POST['password-repeat'])) {
        $_SESSION['feedback'] = "Password is required!";
        header('Location: registration.php');
        die;
    }
    if($_POST['password-repeat'] !== $_POST['password']) {
        $_SESSION['feedback'] = "Passwords do not match";
        header('Location: registration.php');
        die;
    }

    try {
        $connection = new PDO('mysql:host=localhost;dbname=php-authenticatie','root','root');
    } catch (Exception $exception){
        echo $exception->getMessage();
    }

    $selectUserStatement = $connection->prepare('SELECT * FROM users WHERE email = :email');
    $selectUserStatement->bindParam('email',$_POST['email']);
    $selectUserStatement->setFetchMode(PDO::FETCH_ASSOC);
    $selectUserStatement->execute();

    $user = $selectUserStatement->fetch();

    if($user) {
        $_SESSION['feedback'] = "User already exists. Do you want to login?";
        header('Location: registration.php');
        die;
    }

    $hash = password_hash($_POST['password'],PASSWORD_BCRYPT);

    $insertUserStatement = $connection->prepare('INSERT INTO users (email,hash) VALUES (:email,:hash)');
    $insertUserStatement->bindParam('email',$_POST['email']);
    $insertUserStatement->bindParam('hash',$hash);
    $insertUserStatement->execute();


    //todo: Optional: Create session id and set cookie. Redirect to dashboard


    $_SESSION['feedback'] = "Account has been created!";
    header('Location: login.php');
}
?>