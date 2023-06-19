<?php

session_start();


$id = $_SESSION['id'];
$username = $_SESSION['username'];


$conn = new mysqli("localhost", "mkudlinski", "admin123", "bank_database");


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$stmt = $conn->prepare("SELECT balance FROM balance WHERE user_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($balance);
$stmt->fetch();
$stmt->close();


$stmt = $conn->prepare("SELECT sender_id, receiver_id, amount FROM transfers WHERE sender_id = ? OR receiver_id = ?");
$stmt->bind_param("ss", $id, $id);
$stmt->execute();
$result = $stmt->get_result();
$transfers = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Bank Webpage</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1 class="mt-5">Bank Webpage</h1>
    <h2 class="mt-4"><?php echo $username; ?></h2>
    <h3 class="mt-4">Balance: <?php echo $balance; ?></h3>


    <h2 class="mt-4">Previous Transfers</h2>
    <table class="table">
        <thead>
        <tr>
            <th>Sender</th>
            <th>Receiver</th>
            <th>Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($transfers as $transfer) { ?>
            <tr>
                <td><?php echo $transfer['sender_id']; ?></td>
                <td><?php echo $transfer['receiver_id']; ?></td>
                <td><?php echo $transfer['amount']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <a href="transfer.php" class="btn btn-primary mt-4">Start a New Transfer</a>
    <a href="logout.php" class="btn btn-danger mt-4">Logout</a>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>