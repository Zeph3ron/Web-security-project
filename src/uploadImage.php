<?php

session_start();
if (!$_SESSION['authenticated'] === true)
{
    header('location:../loginPage.php');
}
else
{
    require_once dirname(__FILE__) . '../../classes/ValidationHandler.php';
    require_once dirname(__FILE__) . '../../classes/UserHandler.php';
    $uploadErrors = array();
    $targetDir = "../resources/images/";
    $targetImage = $targetDir . basename($_FILES["fileToUpload"]["name"]);
    $imageFileType = pathinfo($targetImage, PATHINFO_EXTENSION);

// Check if image file is a actual image or fake image
    if (isset($_POST["submit"]))
    {
        $validationHandler = new ValidationHandler();
        $image = $_FILES["fileToUpload"]["tmp_name"];
        $validationHandler->validateImage($image, $targetImage, $imageFileType, $uploadErrors);
        if (count($uploadErrors) > 0)
        {
            //If there were any errors, user is informed and no action is taken
            echo '<h3>The following errors occured whilst trying to upload your image!</h3>';
            foreach ($uploadErrors as $error)
            {
                echo "&nbsp;&nbsp;- " . $error . "<br/>";
            }
        }
        else
        {
            $randomImageName = generateRandomString() . '.' . pathinfo($targetImage, PATHINFO_EXTENSION);
            $imagePath = $targetDir . $randomImageName;
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $imagePath))
            {
                echo "Your profile image was successfully updated!";
                echo '<br/>';
                $userHandler = new UserHandler();
                $userId = $_SESSION['user_id'];
                $userHandler->getUserById($userId, $user);
                $oldImagePath = $user->profileImagePath;
                $newImagePath = basename($_FILES["fileToUpload"]["name"]);
                $userHandler->updateUserProfileImage($userId, $randomImageName);

                if ($oldImagePath != 'bill-small.png')
                {
                    unlink('../resources/images/' . $oldImagePath);
                }
            }
            else
            {
                echo "Sorry, there was an unexpected error whilst uploading your profile image.";
            }
        }
    }
    echo '<br/>'
    . '<button class="ui fluid primary button"type="submit" onclick="window.location = \'../editProfilePage.php\'";>Back to edit profile.</button>';
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++)
    {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

?>