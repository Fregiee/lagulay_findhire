<?php
require_once 'dbConfig.php';
require_once 'models.php';

// Register new user
if (isset($_POST['insertNewUserBtn'])) {
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (!empty($name) && !empty($password) && !empty($confirm_password)) {
        if ($password === $confirm_password) {
            $result = insertNewUser($pdo, $name, password_hash($password, PASSWORD_DEFAULT));
            $_SESSION['message'] = $result['message'];
            $_SESSION['status'] = $result['status'];
            header("Location: ../" . ($result['status'] === '200' ? "index.php" : "register.php"));
        } else {
            $_SESSION['message'] = "Passwords do not match.";
            header("Location: ../register.php");
        }
    } else {
        $_SESSION['message'] = "All fields are required.";
        header("Location: ../register.php");
    }
}

// Login user
if (isset($_POST['loginUserBtn'])) {
    $name = trim($_POST['name']);
    $password = trim($_POST['password']);

    if (!empty($name) && !empty($password)) {
        $result = checkIfUserExists($pdo, $name); // Check if user exists

        if ($result['result']) {
            $userInfo = $result['userInfoArray'];

            // Verify the password
            if (password_verify($password, $userInfo['password'])) {
                $_SESSION['HRUser_id'] = $userInfo['HRUser_id'];
                $_SESSION['name'] = $userInfo['name'];
                $_SESSION['message'] = "Login successful!";
                $_SESSION['status'] = "200";

                // Redirect to homePage.php
                header("Location: ../homePage.php");
                exit;
            } else {
                $_SESSION['message'] = "Invalid password.";
                $_SESSION['status'] = "400";
            }
        } else {
            $_SESSION['message'] = "User not found.";
            $_SESSION['status'] = "400";
        }
    } else {
        $_SESSION['message'] = "Both fields are required.";
        $_SESSION['status'] = "400";
    }

    // Redirect back to login page on failure
    header("Location: ../index.php");
    exit;
}
// Create post
if (isset($_SESSION['name']) && isset($_POST['CreatePostBtn'])) {
    $description = trim($_POST['post']);
    if (!empty($description)) {
        $result = insertPost($pdo, $_SESSION['HRUser_id'], $description);
        $_SESSION['message'] = $result['message'];
        header("Location: ../" . ($result['status'] === '200' ? "homePage.php" : "createPost.php"));
    } else {
        $_SESSION['message'] = "Post description cannot be empty.";
        header("Location: ../createPost.php");
    }
}

// Logout user
if (isset($_GET['logoutUserBtn'])) {
    session_unset();
    header("Location: ../index.php");
}

// Edit post
if (isset($_POST['editPostBtn'])) {
    $Post_id = $_POST['Post_id'];
    $description = trim($_POST['description']);

    if (!empty($description)) {
        $result = editPost($pdo, $Post_id, $description);
        $_SESSION['message'] = $result['message'];
        header("Location: ../" . ($result['status'] === '200' ? "homePage.php" : "editpost.php?Post_id=$Post_id"));
    } else {
        $_SESSION['message'] = "Description cannot be empty.";
        header("Location: ../editpost.php?Post_id=$Post_id");
    }
}

// Delete post
if (isset($_GET['Post_id'])) {
    $result = deletePost($pdo, $_GET['Post_id']);
    if ($result['status'] === '200') {
        header("Location: ../homePage.php");
    } else {
        echo "Error: " . $result['message'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['acceptComment']) || isset($_POST['rejectComment'])) {
        $commentId = $_POST['comment_id'];
        $postId = $_POST['post_id'];
        $apUserId = $_POST['ap_user_id']; // Commentator's ID
        $hrUserId = $_SESSION['HRUser_id']; // Current logged-in HR user

        $status = isset($_POST['acceptComment']) ? 1 : 2; // 1 for accept, 2 for reject

        // Insert into application logs
        $query = "INSERT INTO applicationlogs (APUser_id, HRUser_id, post_id, status, date_added) VALUES (:apUserId, :hrUserId, :postId, :status, NOW())";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':apUserId' => $apUserId,
            ':hrUserId' => $hrUserId,
            ':postId' => $postId,
            ':status' => $status
        ]);

        if ($status === 1) {
            // Update the post to save accepted worker's name
            $updateQuery = "UPDATE post SET accepted_worker = :apUserId WHERE Post_id = :postId";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute([
                ':apUserId' => $apUserId,
                ':postId' => $postId
            ]);
        }

        header("Location: ../homePage.php");
        exit;
    }
}

function getCommentsWithNames($pdo, $Post_id) {
    try {
        $sql = "SELECT c.Comment_id, c.Description, c.date_added, c.file_path, c.APUser_id, u.name
                FROM comments c
                INNER JOIN users u ON c.APUser_id = u.id
                WHERE c.Post_id = :Post_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['Post_id' => $Post_id]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'status' => '200',
            'comments' => $comments
        ];
    } catch (PDOException $e) {
        return [
            'status' => '500',
            'message' => $e->getMessage()
        ];
    }
}


if (isset($_POST['upload'])) {
    $targetDir = "uploads/";
    $fileName = basename($_FILES["file"]["name"]);
    $targetFilePath = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
        // Save the file path to the database
        $stmt = $pdo->prepare("INSERT INTO comments (APUser_id, Post_id, Description, file_path) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $postId, $description, $targetFilePath]);
    } else {
        echo "Error uploading file.";
    }
}

?>
