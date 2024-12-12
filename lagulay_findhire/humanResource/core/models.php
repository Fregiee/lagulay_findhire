<?php
require_once 'dbConfig.php';

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function insertNewUser($pdo, $name, $password) {
    $response = array();
    $checkIfUserExists = checkIfUserExists($pdo, $name);

    if (!$checkIfUserExists['result']) {
        $sql = "INSERT INTO humanresource (name, password) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([$name, $password])) {
            $response = array(
                "status" => "200",
                "message" => "User successfully inserted!"
            );
        } else {
            $response = array(
                "status" => "400",
                "message" => "An error occurred with the query!"
            );
        }
    } else {
        $response = array(
            "status" => "400",
            "message" => "User already exists!"
        );
    }

    return $response;
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function insertPost($pdo, $HRUser_id, $description) {
    $response = array();

    if (!empty($description)) {
        $sql = "INSERT INTO Post (HRUser_id, Description) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([$HRUser_id, $description])) {
            $response = array(
                "status" => "200",
                "message" => "Post successfully created!"
            );
        } else {
            $response = array(
                "status" => "400",
                "message" => "An error occurred while creating the post."
            );
        }
    } else {
        $response = array(
            "status" => "400",
            "message" => "Post description cannot be empty."
        );
    }

    return $response;
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function checkIfUserExists($pdo, $name) {
    $response = array();
    $sql = "SELECT * FROM humanresource WHERE name = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$name])) {
        $userInfoArray = $stmt->fetch();

        if ($stmt->rowCount() > 0) {
            $response = array(
                "result" => true,
                "status" => "200",
                "userInfoArray" => $userInfoArray
            );
        } else {
            $response = array(
                "result" => false,
                "status" => "400",
                "message" => "User doesn't exist in the database."
            );
        }
    }

    return $response;
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getAllPosts($pdo) {
    $response = array();
    $sql = "
    SELECT p.Post_id, p.Description, p.date_added, h.name
    FROM Post p
    INNER JOIN humanresource h ON p.HRUser_id = h.HRUser_id
    ORDER BY p.date_added DESC
";

    $stmt = $pdo->prepare($sql);

    if ($stmt->execute()) {
        $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response = array(
            "status" => "200",
            "posts" => $posts
        );
    } else {
        $response = array(
            "status" => "400",
            "message" => "Error fetching posts."
        );
    }

    return $response;
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function deletePost($pdo, $Post_id) {
    $response = array();

    try {
        $pdo->beginTransaction();

        $deleteCommentsSql = "DELETE FROM comments WHERE Post_id = ?";
        $commentsStmt = $pdo->prepare($deleteCommentsSql);
        $commentsStmt->execute([$Post_id]);

        $deletePostSql = "DELETE FROM Post WHERE Post_id = ?";
        $postStmt = $pdo->prepare($deletePostSql);
        $postStmt->execute([$Post_id]);

        $pdo->commit();

        $response = array(
            "status" => "200",
            "message" => "Post and its comments deleted successfully!"
        );
    } catch (Exception $e) {
        $pdo->rollBack();
        $response = array(
            "status" => "400",
            "message" => "Error deleting the post and its comments: " . $e->getMessage()
        );
    }

    return $response;
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getPostById($pdo, $Post_id) {
    $response = array();

    $sql = "SELECT * FROM Post WHERE Post_id = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$Post_id])) {
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($post) {
            $response = array(
                "status" => "200",
                "post" => $post
            );
        } else {
            $response = array(
                "status" => "400",
                "message" => "Post not found."
            );
        }
    } else {
        $response = array(
            "status" => "400",
            "message" => "Error fetching the post."
        );
    }

    return $response;
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function editPost($pdo, $Post_id, $description) {
    $response = array();

    $sql = "UPDATE Post SET Description = ? WHERE Post_id = ?";
    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$description, $Post_id])) {
        $response = array(
            "status" => "200",
            "message" => "Post description updated successfully!"
        );
    } else {
        $response = array(
            "status" => "400",
            "message" => "Error updating the post description."
        );
    }

    return $response;
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function getComments($pdo, $Post_id) {
    $response = array();

    $sql = "
    SELECT c.Comment_id, c.APUser_id, c.Description, c.file_path, c.date_added, u.name
    FROM comments c
    INNER JOIN applicant u ON c.APUser_id = u.APUser_id
    WHERE c.Post_id = ?
    ORDER BY c.date_added DESC
    ";

    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([$Post_id])) {
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $response = array(
            "status" => "200",
            "comments" => $comments
        );
    } else {
        $response = array(
            "status" => "400",
            "message" => "Error fetching comments."
        );
    }

    return $response;
}
?>
