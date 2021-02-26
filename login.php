<?php
session_start();
if(isset($_COOKIE['auth'])){
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

    if($user) {
        header('Location: index.php');
        die;
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
</head>
<body>
<h1>LOGIN</h1>
<?php if (isset($_SESSION['feedback'])): ?>
    <p><?=$_SESSION['feedback']?></p>
<?php endif; ?>
<form method="post" action="login-action.php">
    <label for="email">E-mail</label><br>
    <input type="email" name="email" id="email" required><br>

    <label for="password">Password</label><br>
    <input type="password" name="password" id="password" required><br>

    <button>Login!</button>
</form>
</body>
</html>
