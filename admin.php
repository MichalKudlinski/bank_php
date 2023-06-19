    <?php
    session_start();

    if (!isset($_SESSION['username']) || $_SESSION['permission'] !== 'admin') {
        header("Location: login.php");
        exit();
    }

    $conn = new mysqli("localhost","mkudlinski", "admin123", "bank_database");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get the form data
        $acceptId = $_POST['accept_id'];
        $rejectId = $_POST['reject_id'];


        if (!empty($acceptId)) {
            $acceptIds = implode(",", $acceptId);
            $acceptQuery = "UPDATE worker_accepts SET accept = 1 WHERE id IN ($acceptIds)";
            $conn->query($acceptQuery);

            $workerPermissionQuery = "UPDATE users SET permission = 'worker' WHERE id IN (SELECT user_id FROM worker_accepts WHERE id IN ($acceptIds))";
            $conn->query($workerPermissionQuery);
            // Delete the accepted worker_accepts objects
            $deleteQuery = "DELETE FROM worker_accepts WHERE id IN ($acceptIds)";
            $conn->query($deleteQuery);
        }

        if (!empty($rejectId)) {
            $rejectIds = implode(",", $rejectId);
            $rejectQuery = "DELETE FROM worker_accepts WHERE id IN ($rejectIds)";
            $conn->query($rejectQuery);
        }
    }

    $workerAcceptsQuery = "SELECT id, user_id FROM worker_accepts WHERE accept = 0";
    $workerAcceptsResult = $conn->query($workerAcceptsQuery);

    $conn->close();
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Admin</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body>
    <div class="container">
        <h1 class="mt-5">Admin Panel</h1>

        <h2 class="mt-4">Worker Accepts</h2>

        <?php if ($workerAcceptsResult->num_rows > 0) : ?>
            <form method="POST">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Username</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $workerAcceptsResult->fetch_assoc()) : ?>
                        <tr>
                            <td><?php echo $row['user_id']; ?></td>
                            <td>
                                <input type="checkbox" name="accept_id[]" value="<?php echo $row['id']; ?>">
                                <label class="mr-3">Accept</label>
                                <input type="checkbox" name="reject_id[]" value="<?php echo $row['id']; ?>">
                                <label>Reject</label>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        <?php else : ?>
            <p>No pending worker accepts.</p>
        <?php endif; ?>

        <a href="logout.php" class="btn btn-danger mt-4">Logout</a>
    </div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Create User</h4>
        <a href="user_crud.php" class="btn btn-primary">Manage Users</a>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>