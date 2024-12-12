<?php  
require_once 'core/dbConfig.php';
require_once 'core/handleForms.php';
?>

<?php  
if (!isset($_SESSION['name'])) {
	header("Location: index.php");
}

?>


<html>
    <head>
        <title>
            Le.Lagulay Agente
        </title>
    </head>
    <body>
        <h1> Create Post </h1>
        <form action="core/handleForms.php" method="POST">
        <textarea id="message" name="post" rows="4" cols="50"></textarea>
        <input type="submit" name="CreatePostBtn" value="Submit">
		</p>
	</form>
    </body>
</html>