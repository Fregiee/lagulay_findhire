<?php  
require_once 'dbConfig.php';
require_once 'models.php';


//register
if (isset($_POST['insertNewUserBtn'])) {
	$name = trim($_POST['name']);
	$password = trim($_POST['password']);
	$confirm_password = trim($_POST['confirm_password']);

	if (!empty($name) && !empty($password) && !empty($confirm_password)) {

		if ($password == $confirm_password) {

			$insertQuery = insertNewUser($pdo, $name, password_hash($password, PASSWORD_DEFAULT));
			$_SESSION['message'] = $insertQuery['message'];

			if ($insertQuery['status'] == '200') {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../index.php");
			}

			else {
				$_SESSION['message'] = $insertQuery['message'];
				$_SESSION['status'] = $insertQuery['status'];
				header("Location: ../register.php");
			}

		}
		else {
			$_SESSION['message'] = "Please make sure both passwords are equal";
			$_SESSION['status'] = '400';
			header("Location: ../register.php");
		}

	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}
}
//login
if (isset($_POST['loginUserBtn'])) {
	$name = trim($_POST['name']);
	$password = trim($_POST['password']);

	if (!empty($name) && !empty($password)) {

		$loginQuery = checkIfUserExists($pdo, $name);
		$userIDFromDB = $loginQuery['userInfoArray']['APUser_id'];
		$nameFromDB = $loginQuery['userInfoArray']['name'];
		$passwordFromDB = $loginQuery['userInfoArray']['password'];

		if (password_verify($password, $passwordFromDB)) {
			$_SESSION['APUser_id'] = $userIDFromDB;
			$_SESSION['name'] = $nameFromDB;
			header("Location: ../homePage.php");
		}

		else {
			$_SESSION['message'] = "name/password invalid";
			$_SESSION['status'] = "400";
			header("Location: ../index.php");
		}
	}

	else {
		$_SESSION['message'] = "Please make sure there are no empty input fields";
		$_SESSION['status'] = '400';
		header("Location: ../register.php");
	}

}
if (isset($_POST['submitComment'])) {
    $Post_id = $_POST['Post_id'];
    $commentDescription = trim($_POST['commentDescription']);
    $filePath = null;
    $APUser_id = $_SESSION['APUser_id']; // Get the logged-in user's ID

    // Handle file upload
    if (isset($_FILES['fileUpload']) && $_FILES['fileUpload']['error'] == 0) {
        // Define the upload directory and allowed file types
        $uploadDir = '../../uploads/';
        $allowedFileTypes = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword'];

        // Check file type
        if (in_array($_FILES['fileUpload']['type'], $allowedFileTypes)) {
            $fileName = basename($_FILES['fileUpload']['name']);
            $filePath = $uploadDir . $fileName;

            // Move the uploaded file to the desired folder
            if (move_uploaded_file($_FILES['fileUpload']['tmp_name'], $filePath)) {
                echo "File uploaded successfully.";
            } else {
                echo "File upload failed.";
            }
        } else {
            echo "Invalid file type.";
        }
    }

    // Insert the comment into the database with APUser_id
    $result = insertComment($pdo, $Post_id, $APUser_id, $commentDescription, $filePath);

    // Redirect back to the homepage
    if ($result['status'] == '200') {
        header("Location: ../homePage.php");
        exit;
    } else {
        echo "Error: " . $result['message'];
    }
}

//logout
if (isset($_GET['logoutUserBtn'])) {
	unset($_SESSION['HRUser_id']);
	unset($_SESSION['name']);
	header("Location: ../index.php");
}
?>