<?php
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $email = $_POST['email'];
    $isWorker = isset($_POST['is_worker']) && $_POST['is_worker'] === 'on';

    $password_val = '/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^*()\-_+{};:,<.>]).{8,}$/';

    if(!preg_match($password_val, $confirmPassword)) {
        $error = "Password must be at least 8 characters long, contain one or more uppercase letter, one digit
        and one special character!";
    }elseif($password !== $confirmPassword){
        $error = "Password and confirm password are not identical!";
    }elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = 'Invalid email format!';

    }else {
        $conn = new mysqli("localhost", "mkudlinski", "admin123", "bank_database");

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }


        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt -> bind_param("s",$username);
        $stmt -> execute();
        $stmt -> store_result();

        if ($stmt->num_rows>0){
            $error = "Username already taken.";
        }else{
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, password, permission, email) VALUES(?,?,?,?)");
            $stmt->bind_param("ssss",$username, $hashedPassword, $permission, $email);


            if($isWorker){
                $permission = null;
            }else{
                $permission = 'user';
            }


            $stmt -> execute();
            $stmt ->close();
            if(!$isWorker){
                $userId = $conn->insert_id;
                $stmt2 = $conn ->prepare("INSERT INTO balance (user_id, balance) VALUES (?,1000)");
                $stmt2-> bind_param("i",$userId);
                $stmt2-> execute();
                $stmt2-> close();
            }

            if($isWorker){
                $userId = $conn->insert_id;
                $stmt2 = $conn ->prepare("INSERT INTO worker_accepts (user_id) VALUES (?)");
                $stmt2-> bind_param("i",$userId);
                $stmt2-> execute();
                $stmt2-> close();
            }


            header("Location: login.php");
            exit();
        }

    }
    header("Location:register.php?error=". urlencode($error));
    exit();

}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <h1 class = "mt-5"> Registration </h1>
    <?php if (isset($_GET['error'])){?>
        <div class = "alert alert-danger mt-4">
            <?php echo $_GET['error']; ?>

        </div>
    <?php } ?>

    <form method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username </label>
            <input type="text" class="form-control" id="username", name="username"  required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label"> Email </label>
            <input type="email" class="form-control" id="email", name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password", name="password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password", name="confirm_password", required>

        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="is_worker", name="is_worker">
            <label class="form-check-label" for="is_worker">Register as a worker</label>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

</body>
</html>