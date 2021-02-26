<?php

session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(!isset($_POST['email'])) {
        $_SESSION['feedback'] = "Email is required!";
    }
    if(!isset($_POST['password'])) {
        $_SESSION['feedback'] = "Password is required!";
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

    if(!$user) {
        $_SESSION['feedback'] = 'These credentials do not match our records.';
        header('Location: login.php');
        die;
    }

    if(!password_verify($_POST['password'],$user['hash'])) {
        $_SESSION['feedback'] = 'These credentials do not match our records.';
        header('Location: login.php');
        die;
    }

    $userSessionId = uniqid();


    $updateUserSessionIdStatement = $connection->prepare('UPDATE users SET session_id = :sessionId WHERE email = :email');
    $updateUserSessionIdStatement->bindParam('sessionId',$userSessionId);
    $updateUserSessionIdStatement->bindParam('email',$_POST['email']);
    $updateUserSessionIdStatement->execute();

    setcookie('auth',$userSessionId,time() + 3600,'','','',true);

    header('Location: index.php');
}
?>