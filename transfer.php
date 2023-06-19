<?php



if (!isset($_SESSION['username'])) {

    header("Location: login.php");
    exit();
}


$username = $_SESSION['username'];
$id = $_SESSION['id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $receiver = $_POST['receiver'];
    $amount = $_POST['amount'];


    $conn = new mysqli("localhost","mkudlinski", "admin123", "bank_database");


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $receiver);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {

        $conn->close();
        header("Location: transfer.php?error=user");
        exit();
    }

    $stmt->bind_result($receiverId);
    $stmt->fetch();
    $stmt->close();


    $stmt = $conn->prepare("SELECT balance FROM balance WHERE user_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($senderBalance);
    $stmt->fetch();
    $stmt->close();

    if ($senderBalance < $amount) {

        $conn->close();
        header("Location: transfer.php?error=balance");
        exit();
    }


    $stmt = $conn->prepare("INSERT INTO transfers (sender_id, receiver_id, amount) VALUES (?, ?, ?)");
    $stmt->bind_param("isd", $id, $receiverId, $amount);
    $stmt->execute();


    $stmt = $conn->prepare("UPDATE balance SET balance = balance + ? WHERE user_id = ?");
    $stmt->bind_param("ds", $amount, $receiverId);
    $stmt->execute();


    $stmt = $conn->prepare("UPDATE balance SET balance = balance - ? WHERE user_id = ?");
    $stmt->bind_param("di", $amount, $id);
    $stmt->execute();

    $stmt->close();
    $conn->close();


    header("Location: user.php");
    exit();
}


$error = isset($_GET['error']) ? $_GET['error'] : "";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Transfer</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1 class="mt-5">Create Transfer</h1>

    <?php if ($error === 'balance') { ?>
        <div class="alert alert-danger mt-4">Your balance is too low to make this transfer.</div>
    <?php } elseif ($error === 'user') { ?>
        <div class="alert alert-danger mt-4">The specified receiver does not exist.</div>
    <?php } ?>

    <form method="POST">
        <div class="form-group">
            <label for="receiver">Receiver</label>
            <input type="text" class="form-control" id="receiver" name="receiver" required>
        </div>
        <div class="form-group">
            <label for="amount">Amount</label>
            <input type="number" class="form-control" id="amount" name="amount" required>
        </div>
        <button type="submit" class="btn btn-primary">Transfer</button>
    </form>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>