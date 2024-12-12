<?php
require_once 'core/dbConfig.php';
require_once 'core/handleForms.php';

// Check if user is logged in
if (isset($_SESSION['APUser_id'])) {
    $userId = $_SESSION['APUser_id'];
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Page</title>
</head>
<body>
    <h1>Welcome</h1>
    <a href="messageHr.php">Message a Representative</a> | 
    <a href="core/handleForms.php?logoutUserBtn=1">Logout</a>

    <?php
    $result = getAllPosts($pdo); // Get all posts
    if ($result['status'] === '200'):
        foreach ($result['posts'] as $post): 
    ?>
        <div style="border: 1px solid #ccc; margin-bottom: 15px; padding: 10px;">
            <h3><?php echo htmlspecialchars($post['name']); ?> (Uploader)</h3>
            <p><strong>Post ID:</strong> <?php echo $post['Post_id']; ?></p>
            <p><strong>Description:</strong> <?php echo htmlspecialchars($post['Description']); ?></p>
            <p><strong>Date Added:</strong> <?php echo $post['date_added']; ?></p>

            <!-- Comment Form -->
            <h4>Leave a Comment:</h4>
            <form action="core/handleForms.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="Post_id" value="<?php echo $post['Post_id']; ?>">
                <textarea name="commentDescription" rows="4" cols="50" placeholder="Write your comment here..."></textarea><br><br>
                <label for="fileUpload">Upload a file:</label>
                <input type="file" name="fileUpload" accept="image/*, .pdf, .docx"><br><br>
                <button type="submit" name="submitComment">Post Comment</button>
            </form>

            <!-- Display Comments -->
            <?php
            $commentsResult = getComments($pdo, $post['Post_id']);
            if ($commentsResult['status'] === '200' && !empty($commentsResult['comments'])):
            ?>
                <h4>Comments:</h4>
                <?php foreach ($commentsResult['comments'] as $comment): ?>
                    <div style="border: 1px solid #ddd; margin-top: 10px; padding: 5px;">
                        <p><strong>User <?php echo htmlspecialchars($comment['APUser_id']); ?>:</strong></p>
                        <p><?php echo htmlspecialchars($comment['Description']); ?></p>
                        <small>Posted on: <?php echo $comment['date_added']; ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No comments yet.</p>
            <?php endif; ?>
        </div>
    <?php
        endforeach;
    else:
    ?>
        <p>Error fetching posts.</p>
    <?php endif; ?>
</body>
</html>
