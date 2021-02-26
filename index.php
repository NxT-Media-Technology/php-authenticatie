<?php
if(!isset($_COOKIE['auth'])){
    header('Location: login.php');
    die;
}

try {
    $connection = new PDO('mysql:host=localhost;dbname=php-authenticatie','root','root');
} catch (Exception $exception){
    echo $exception->getMessage();
}

$selectUserStatement = $connection->prepare('SELECT * FROM users WHERE session_id = :sessionId');
$selectUserStatement->bindParam('sessionId',$_COOKIE['auth']);
$selectUserStatement->setFetchMode(PDO::FETCH_ASSOC);
$selectUserStatement->execute();

$user = $selectUserStatement->fetch();

if(!$user) {
    header('Location: login.php');
    die;
}
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
