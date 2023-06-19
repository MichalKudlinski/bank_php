<?php
// Start the session
session_start();

// Check if the user is logged in and is a worker
if (!isset($_SESSION['id']) || $_SESSION['permission'] !== 'worker') {
    // Redirect to the login page or show an error message
    header("Location: login.php");
    exit();
}

// Connect to the database (replace "hostname", "username", "password", and "database" with your own details)
$conn = new mysqli("localhost","mkudlinski", "admin123", "bank_database");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve user information from the database
$usersQuery = "SELECT u.id, u.email, b.balance FROM users u LEFT JOIN balance b ON u.id = b.user_id";
$usersResult = $conn->query($usersQuery);

// Retrieve transfer history from the database
$transfersQuery = "SELECT t.sender_id, t.receiver_id, t.amount FROM transfers t";
$transfersResult = $conn->query($transfersQuery);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Worker</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1 class="mt-5">Worker Panel</h1>

    <h2 class="mt-4">User Information</h2>

    <?php if ($usersResult->num_rows > 0) : ?>
        <table class="table">
            <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Balance</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $usersResult->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['balance']; ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No users found.</p>
    <?php endif; ?>

    <h2 class="mt-4">Transfer History</h2>

    <?php if ($transfersResult->num_rows > 0) : ?>
        <table class="table">
            <thead>
            <tr>
                <th>Sender</th>
                <th>Receiver</th>
                <th>Amount</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $transfersResult->fetch_assoc()) : ?>
                <tr>
                    <td><?php echo $row['sender_id']; ?></td>
                    <td><?php echo $row['receiver_id']; ?></td>
                    <td><?php echo $row['amount']; ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>No transfer history found.</p>
    <?php endif; ?>

    <a href="logout.php" class="btn btn-danger mt-4">Logout</a>
</div>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>