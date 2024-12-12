<?php  

require_once 'core/dbConfig.php';
require_once 'core/handleForms.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <a href="../index.php">Go Back</a>

    <!-- Display message if set -->
    <?php if (isset($_SESSION['message'])): ?>
        <h1 style="color: <?= $_SESSION['status'] === '200' ? 'green' : 'red'; ?>">
            <?= $_SESSION['message']; ?>
        </h1>
        <?php unset($_SESSION['message'], $_SESSION['status']); ?>
    <?php endif; ?>

    <h1>Login Now!</h1>
    <form action="core/handleForms.php" method="POST">
        <p>
            <label for="username">Name</label>
            <input type="text" name="name" required>
        </p>
        <p>
            <label for="password">Password</label>
            <input type="password" name="password" required>
        </p>
        <p>
            <input type="submit" name="loginUserBtn" value="Login">
        </p>
    </form>
    <p>Don't have an account? You may register <a href="register.php">here</a></p>
</body>
</html>