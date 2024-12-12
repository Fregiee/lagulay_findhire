<?php  
require_once 'dbConfig.php';
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function insertNewUser($pdo, $name, $password) {
	$response = array();
	$checkIfUserExists = checkIfUserExists($pdo, $name); 

	if (!$checkIfUserExists['result']) {

		$sql = "INSERT INTO applicant (name,password) 
		VALUES (?,?)";

		$stmt = $pdo->prepare($sql);

		if ($stmt->execute([$name, $password])) {
			$response = array(
				"status" => "200",
				"message" => "User successfully inserted!"
			);
		}

		else {
			$response = array(
				"status" => "400",
				"message" => "An error occured with the query!"
			);
		}
	}

	else {
		$response = array(
			"status" => "400",
			"message" => "User already exists!"
		);
	}

	return $response;
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function checkIfUserExists($pdo, $name) {
	$response = array();
	$sql = "SELECT * FROM applicant WHERE name = ?";
	$stmt = $pdo->prepare($sql);

	if ($stmt->execute([$name])) {

		$userInfoArray = $stmt->fetch();

		if ($stmt->rowCount() > 0) {
			$response = array(
				"result"=> true,
				"status" => "200",
				"userInfoArray" => $userInfoArray
			);
		}

		else {
			$response = array(
				"result"=> false,
				"status" => "400",
				"message"=> "User doesn't exist from the database"
			);
		}
	}

	return $response;

}
function getAllPosts($pdo) {
    $response = array();

    // Make sure to join Post table with humanresource table on the correct column
    $sql = "
        SELECT p.Post_id, p.Description, p.date_added, h.name
        FROM Post p
        INNER JOIN humanresource h ON p.HRUser_id = h.HRUser_id
        ORDER BY p.date_added DESC
    ";

    $stmt = $pdo->prepare($sql);

    if ($stmt->execute()) {
        // Fetch all posts
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

function insertComment($pdo, $Post_id, $APUser_id, $commentDescription, $filePath = null) {
    try {
        $query = "INSERT INTO comments (Post_id, APUser_id, Description, file_path) 
                  VALUES (:Post_id, :APUser_id, :Description, :file_path)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':Post_id' => $Post_id,
            ':APUser_id' => $APUser_id,
            ':Description' => $commentDescription,
            ':file_path' => $filePath,
        ]);

        return ['status' => '200', 'message' => 'Comment inserted successfully.'];
    } catch (PDOException $e) {
        return ['status' => '400', 'message' => $e->getMessage()];
    }
}


// Function to get comments for a post
function getComments($pdo, $Post_id) {
    $response = array();

    $sql = "SELECT * FROM comments WHERE Post_id = ? ORDER BY date_Added DESC";
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