<?php
//Check if auth cookie is set. If not redirect to login page
if(!isset($_COOKIE['auth'])){
    header('Location: login.php');
    die;
}

//Connect with database
try {
    $connection = new PDO('mysql:host=localhost;dbname=php-authenticatie','root','root');
} catch (Exception $exception){
    echo $exception->getMessage();
}

//Check if a user with the session id from the cookie exists in our database
$selectUserStatement = $connection->prepare('SELECT * FROM users WHERE session_id = :sessionId');
$selectUserStatement->bindParam('sessionId',$_COOKIE['auth']);
$selectUserStatement->setFetchMode(PDO::FETCH_ASSOC);
$selectUserStatement->execute();

$user = $selectUserStatement->fetch();

//No user exists. Redirect to login page
if(!$user) {
    header('Location: login.php');
    die;
}

//Show the dashboard for the user
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard</title>
</head>
<body>
<h1>DASHBOARD</h1>
<h2>Welcome: <?=$user['email']?></h2>
</body>
</html>
