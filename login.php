<?php
session_start();
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $conn = new mysqli("localhost", "mkudlinski", "admin123","bank_database");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT id, password,permission FROM users WHERE username = ?");
    $stmt -> bind_param("s",$username);
    $stmt -> execute();
    $stmt -> bind_result($id,$hashedPassword, $permission);
    $stmt -> fetch();
    $stmt -> close();

    if(password_verify($password, $hashedPassword)){
        $_SESSION['username'] = $username;
        $_SESSION['permission'] = $permission;
        $_SESSION['id'] = $id;

        if($permission === 'admin'){
            header("Location: admin.php");
            exit();
        }elseif($permission === 'worker'){
            headher("Location: worker.php");
        }elseif($permission === 'user'){
            header("Location: user.php");
            exit();
        }else{
            $error = "Admin has not accepted your request yet";
        }

    }else{
        $error = "Invalid username or password :(";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <title>Login</title>

</head>
<body>
<div class = "container">
    <h1 class="mt-5">Login</h1>
    <?php if (isset($error)): ?>
        <div class=" alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username </label>
            <input type="text" class="form-control" id="username", name="username"  required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label"> Password </label>
            <input type="password" class="form-control" id="password", name="password" required>
        </div>
        <button type="submit" class="btn btn-primary"> Login </button>

    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
</body>
</html>

