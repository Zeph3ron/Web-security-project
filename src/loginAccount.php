<?php

require_once dirname(__FILE__) . '../../classes/ValidationHandler.php';
require_once dirname(__FILE__) . '../../classes/UserHandler.php';

$valErrors = array();
$username = $_POST["Username"];
$password = $_POST["Password"];

$valHandler = new ValidationHandler();
$valHandler->validateField($username, "Username", $valErrors);
$valHandler->validateField($password, "Password", $valErrors);

if (count($valErrors) > 0)
{
    //If there were any errors, user is informed and no action is taken
    echo '<h3>The following errors occured whilst trying to login!</h3>';
    foreach ($valErrors as $error)
    {
        echo "&nbsp;&nbsp;- " . $error . "<br/>";
    }
}
else
{
    $loginErrors = array();
    $userHandler = new UserHandler();
    $userHandler->loginUser($username, $password, $loginErrors);
    if (count($loginErrors) > 0)
    {
        echo '<h3>The following errors occured whilst trying to login!</h3>';
        foreach ($loginErrors as $error)
        {
            echo "&nbsp;&nbsp;- " . $error . "<br/>";
        }
    }
    else
    {
        $userId = $_SESSION['user_id'];
        $userHandler->getUserById($userId, $user);
        if ($user->isBanned)
        {
            echo 'Your account has been banned because of inappropriate behaviour. If you want to protest this action, please send an email to our support: don@counton.it';
        }
        else
        {
            $_SESSION['authenticated'] = true;
            header('location:../mainWallPage.php');
        }
    }
}