<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

// Check if Post_id is set in the URL
if (isset($_GET['Post_id'])) {
    $Post_id = $_GET['Post_id'];

    // Fetch the current post data to pre-fill the form
    $postData = getPostById($pdo, $Post_id);

    if ($postData['status'] == '200') {
        $post = $postData['post'];
    } else {
        echo "Error fetching post data.";
        exit;
    }
} else {
    echo "No Post_id specified!";
    exit;
}

?>

<form action="core/handleForms.php" method="POST">
    <input type="hidden" name="Post_id" value="<?php echo $post['Post_id']; ?>">
    <label for="description">Edit Description:</label><br>
    <textarea name="description" rows="4" cols="50"><?php echo htmlspecialchars($post['description']); ?></textarea><br><br>
    <input type="submit" name="editPostBtn" value="Save Changes">
</form>
