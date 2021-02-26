<?php

session_start();

//Check if POST and not GET
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Check if email is set. If not show feedback to user on login page
    if(!isset($_POST['email'])) {
        $_SESSION['feedback'] = "Email is required!";
        header('Location: login.php');
        die;
    }
    //Check if password is set. If not show feedback to user on login page
    if(!isset($_POST['password'])) {
        $_SESSION['feedback'] = "Password is required!";
        header('Location: login.php');
        die;
    }

    //Connect with database
    try {
        $connection = new PDO('mysql:host=localhost;dbname=php-authenticatie','root','root');
    } catch (Exception $exception){
        echo $exception->getMessage();
    }

    //Check if user with given email exists in database
    $selectUserStatement = $connection->prepare('SELECT * FROM users WHERE email = :email');
    $selectUserStatement->bindParam('email',$_POST['email']);
    $selectUserStatement->setFetchMode(PDO::FETCH_ASSOC);
    $selectUserStatement->execute();

    $user = $selectUserStatement->fetch();

    //User does not exist cannot login. Should create an account
    if(!$user) {
        $_SESSION['feedback'] = 'These credentials do not match our records.';
        header('Location: login.php');
        die;
    }

    //Check if user input in password field hashed is the same as the has from our database. If not password is incorrect
    if(!password_verify($_POST['password'],$user['hash'])) {
        $_SESSION['feedback'] = 'These credentials do not match our records.';
        header('Location: login.php');
        die;
    }

    //Create session id for user
    $userSessionId = uniqid();


    //Save session id for user in database
    $updateUserSessionIdStatement = $connection->prepare('UPDATE users SET session_id = :sessionId WHERE email = :email');
    $updateUserSessionIdStatement->bindParam('sessionId',$userSessionId);
    $updateUserSessionIdStatement->bindParam('email',$_POST['email']);
    $updateUserSessionIdStatement->execute();

    //Save session id for user in cookie
    setcookie('auth',$userSessionId,time() + 3600,'','','',true);

    //Redirect to dashboard
    header('Location: index.php');
}
?>