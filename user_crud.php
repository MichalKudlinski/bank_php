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


if (isset($_POST['delete'])) {
    $userID = $_POST['userID'];
    $deleteQuery = "DELETE FROM users WHERE id = '$userID'";
    $conn->query($deleteQuery);
    header("Location: user_crud.php");
    exit();
}


if (isset($_POST['add'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $permission = $_POST['permission'];


    $passwordPattern = '/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()\-_=+{};:,<.>]).{8,}$/';
    if (!preg_match($passwordPattern, $password)) {
        $error = "Password must be at least 8 characters long, contain at least one uppercase letter, one digit, and one special character.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {

        $stmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username already taken.";
        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


            $stmt = $conn->prepare("INSERT INTO users (username, email, password, permission) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $username, $email, $hashedPassword, $permission);
            $stmt->execute();


            $newUserID = $stmt->insert_id;


            $balanceQuery = "INSERT INTO balance (user_id, balance) VALUES ('$newUserID', 1000)";
            $conn->query($balanceQuery);



            header("Location: user_crud.php");
            exit();
        }

        $stmt->close();
    }
}


if (isset($_POST['update'])) {
    $userID = $_POST['userID'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $permission = $_POST['permission'];


    $passwordPattern = '/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()\-_=+{};:,<.>]).{8,}$/';
    if (!preg_match($passwordPattern, $password)) {
        $error = "Password must be at least 8 characters long, contain at least one uppercase letter, one digit, and one special character.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {

        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $username, $userID);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username already taken.";
        } else {

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);


            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, permission = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $username, $email, $hashedPassword, $permission, $userID);
            $stmt->execute();


            header("Location: user_crud.php");
            exit();
        }

        $stmt->close();
    }
}


$selectQuery = "SELECT * FROM users";
$userResult = $conn->query($selectQuery);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User CRUD</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
<div class="container mt-4">
    <h1>User CRUD</h1>

 >
    <h4>Create User</h4>
    <form method="POST">
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php } ?>
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="permission">Permission:</label>
            <select name="permission" class="form-control" required>
                <option value="user">User</option>
                <option value="worker">Worker</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" name="add" class="btn btn-primary">Add User</button>
    </form>

    <!-- Users List -->
    <h4 class="mt-4">Users List</h4>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Permission</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = $userResult->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['permission']; ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="userID" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="delete" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editModal-<?php echo $row['id']; ?>">Edit</button>
                </td>
            </tr>
            <!-- Edit User Modal -->
            <div class="modal fade" id="editModal-<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel-<?php echo $row['id']; ?>" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel-<?php echo $row['id']; ?>">Edit User</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form method="POST">
                                <?php if (isset($error)) { ?>
                                    <div class="alert alert-danger"><?php echo $error; ?></div>
                                <?php } ?>
                                <div class="form-group">
                                    <label for="username">Username:</label>
                                    <input type="text" name="username" class="form-control" value="<?php echo $row['username']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo $row['email']; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password:</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="permission">Permission:</label>
                                    <select name="permission" class="form-control" required>
                                        <option value="user" <?php echo ($row['permission'] === 'user') ? 'selected' : ''; ?>>User</option>
                                        <option value="worker" <?php echo ($row['permission'] === 'worker') ? 'selected' : ''; ?>>Worker</option>
                                        <option value="admin" <?php echo ($row['permission'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                </div>
                                <input type="hidden" name="userID" value="<?php echo $row['id']; ?>">
                                <button type="submit" name="update" class="btn btn-primary">Update</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
        </tbody>
    </table>

    <a href="admin.php" class="btn btn-secondary">Back</a>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>