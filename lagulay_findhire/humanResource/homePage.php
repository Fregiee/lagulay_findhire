<?php
require_once 'core/dbConfig.php';
require_once 'core/handleForms.php';

if (isset($_SESSION['HRUser_id'])) {
    $userId = $_SESSION['HRUser_id'];
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
    <a href="createPost.php">Create a post</a> | 
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

            <!-- Check and Display Uploaded File -->
            <?php if (!empty($comment['file_path'])): ?>
                <p>
                    <strong>File:</strong> 
                    <a href="<?php echo htmlspecialchars($comment['file_path']); ?>" download>
                        Download File
                    </a>
                </p>
            <?php endif; ?>

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
            
                        <!-- Check and Display File Path for Comment -->
                        <?php if (!empty($comment['file_path'])): ?>
                            <p>
                                <strong>File:</strong> 
                                <a href="<?php echo htmlspecialchars($comment['file_path']); ?>" download>
                                    Download File
                                </a>
                            </p>
                        <?php endif; ?>
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
