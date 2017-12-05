<?php
session_start();
if (!$_SESSION['authenticated'] === true)
{
    header('location:../loginPage.php');
}
else
{
    require_once dirname(__FILE__) . '../../classes/PostHandler.php';
    require_once dirname(__FILE__) . '../../classes/UserHandler.php';
    if (!isset($_POST['post_id']))
    {
        header('location:../mainWallPage.php');
    }
    else
    {
        $userHandler = new UserHandler();
        $postHandler = new PostHandler();
        $userId = $_SESSION['user_id'];
        $postId = $_POST['post_id'];
        $userVotes = $postHandler ->canVote($userId);
        if ($postHandler ->canVote($userId))
        {
            $postHandler ->voteOnPost($userId, $postId);
            header('location:../displayPostPage.php?post_id='.$postId);
        }
        else
        {
            echo 'You have passed your limit of 5 votes per day, try again tomorrow.';
        }
    }
}