<?php
session_start();

//Check if POST and not GET
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Check if email is set. If not show feedback to user on registration page
    if(!isset($_POST['email'])) {
        $_SESSION['feedback'] = "Email is required!";
        header('Location: registration.php');
        die;
    }
    //Check if password is set. If not show feedback to user on registration page
    if(!isset($_POST['password'])) {
        $_SESSION['feedback'] = "Password is required!";
        header('Location: registration.php');
        die;
    }
    //Check if password-repeat is set. If not show feedback to user on registration page
    if(!isset($_POST['password-repeat'])) {
        $_SESSION['feedback'] = "Password is required!";
        header('Location: registration.php');
        die;
    }
    //Check if two passwords match. If not show feedback to user on registration page
    if($_POST['password-repeat'] !== $_POST['password']) {
        $_SESSION['feedback'] = "Passwords do not match";
        header('Location: registration.php');
        die;
    }

    //Connect with database
    try {
        $connection = new PDO('mysql:host=localhost;dbname=php-authenticatie','root','root');
    } catch (Exception $exception){
        echo $exception->getMessage();
    }

    //Check if user exists in database
    $selectUserStatement = $connection->prepare('SELECT * FROM users WHERE email = :email');
    $selectUserStatement->bindParam('email',$_POST['email']);
    $selectUserStatement->setFetchMode(PDO::FETCH_ASSOC);
    $selectUserStatement->execute();

    $user = $selectUserStatement->fetch();

    //User already exists cannot create account. Should just login
    if($user) {
        $_SESSION['feedback'] = "User already exists. Do you want to login?";
        header('Location: registration.php');
        die;
    }

    //Hash the passwors
    $hash = password_hash($_POST['password'],PASSWORD_BCRYPT);

    //Insert user in database
    $insertUserStatement = $connection->prepare('INSERT INTO users (email,hash) VALUES (:email,:hash)');
    $insertUserStatement->bindParam('email',$_POST['email']);
    $insertUserStatement->bindParam('hash',$hash);
    $insertUserStatement->execute();


    //todo: Optional: Create session id and set cookie. Redirect to dashboard


    //User can now login!
    $_SESSION['feedback'] = "Account has been created!";
    header('Location: login.php');
}
?>