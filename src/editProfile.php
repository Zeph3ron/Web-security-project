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

    $valErrors = array();
    $displayName = $_POST["Display_name"];
    $description = $_POST["User_description"];
    $userId = $_SESSION['user_id'];

    $valHandler = new ValidationHandler();
    $valHandler->validateDisplayName($displayName, $userId, $valErrors);
    $valHandler->validateUserDescription($description, $valErrors);

    if (count($valErrors) > 0)
    {
        echo '<h3>The following errors occured whilst updating your account!</h3>';
        foreach ($valErrors as $error)
        {
            echo "&nbsp;&nbsp;- " . $error . "<br/>";
        }
        echo '<br/>'
        . '<button class="ui fluid primary button"type="submit" onclick="window.location = \'../editProfilePage.php\'";>Back to edit profile.</button>'
        . '</form>';
    }
    else
    {
        $userHandler = new UserHandler();
        $userHandler->updateUserProfile($userId, $displayName, $description);
        header('location: ../mainWallPage.php');
    }
}