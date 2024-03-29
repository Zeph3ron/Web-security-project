<?php

require_once dirname(__FILE__) . '/DatabaseHandler.php';
require_once dirname(__FILE__) . '/UserHandler.php';
require_once dirname(__FILE__) . '../../models/Post.php';
require_once dirname(__FILE__) . '../../models/Comment.php';
require_once dirname(__FILE__) . '../../config.php';

/**
 * Description of PostHandler
 * Handles all operations regarding user posts.
 */
class PostHandler {
    /**
     * Retrieves all Posts in the database as "Post" objects.
     * @return array Array of "Post" objects.
     */
    function getAllPosts()
    {
        $posts = [];
        $dbHandler = $this->getDbHandler();
        $userHandler = new UserHandler();
        $records = $dbHandler->getAllRecords("Post");
        for ($i = 0; $i < count($records); $i++)
        {
            $postOwner = null;
            $userHandler->getUserById($records[$i][1], $postOwner);
            $nrOfVotes = $this->getVotesForPost($records[$i][0]);
            $profileImagePath = $postOwner->profileImagePath;
            array_push($posts, new Post($records[$i][0], $records[$i][1], $records[$i][2], $records[$i][3], $nrOfVotes, $records[$i][4], $profileImagePath));
        }
        return $posts;
    }
    
    /**
     * Retrieves a specific Post from the database as a "Post" object.
     * @param type $postId The id of the post.
     * @return type "Post" object.
     */
    function getPost($postId)
    {
        $posts = $this->getAllPosts();
        $post = null;
        for ($i = 0; $i < count($posts); $i++)
        {
            if ($posts[$i]->id === $postId)
            {
                $post = $posts[$i];
            }
        }
        return $post;
    }

    /**
     * Gets the number of posts a user has posted.
     * @param type $userId The id of the user.
     * @return int The number of posts.
     */
    function getNrOfPosts($userId)
    {
        $posts = $this->getAllPosts();
        $count = 0;
        for ($i = 0; $i < count($posts); $i++)
        {
            if ($posts[$i]->ownerId === $userId)
            {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Creates a post and adds it to the database.
     * @param type $ownerId The userId of the post owner.
     * @param type $title Title of the post.
     * @param type $content The content of the post.
     */
    function createPost($ownerId, $title, $content)
    {
        $dbHandler = $this->getDbHandler();
        $dbHandler->addRecord("Post", ["Owner_id", "Title", "Post_date", "Content"], [$ownerId, $title, date("Y-m-d H:i:s"), $content], [PDO::PARAM_INT, PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR]);
    }

    /**
     * Deletes a post record from the database
     * @param type $postId The id of the post to delete.
     */
    function deletePost($postId)
    {
        $dbHandler = $this->getDbHandler();
        $dbHandler->deleteRecord("Post", "id", $postId, PDO::PARAM_INT);
    }

    /**
     * Updates a post record in the database.
     * @param type $postId The id of the post to update.
     * @param type $title The new title of the post.
     * @param type $content The new content of the post.
     */
    function updatePost($postId, $title, $content)
    {
        $dbHandler = $this->getDbHandler();
        $dbHandler->updateRecord("Post", "Title", $title, "Id", $postId, PDO::PARAM_STR);
        $dbHandler->updateRecord("Post", "Content", $content, "Id", $postId, PDO::PARAM_STR);
    }

    /**
     * Returns the html that displays the post correctly on the site.
     * @param type $post The post to display
     * @return type html string that displays the post.
     */
    function getPostHtml($post)
    {
        return '<div class="item">'
                . '<img class="ui avatar image" src="src/getProfileImage.php?image=' . $post->profileImagePath . '">'
                . '<div class="content">'
                . '<a class="header" href="displayPostPage.php?post_id=' . $post->id . '">' . $post->title . '</a><font size="1">Created on ' . date_format($post->postDate, 'd/m/Y H:i') . '</font>'
                . '</div>'
                . '<font size="1" style="float:right"> Up Votes: ' . $post->nrOfVotes . '</font>'
                . '</div>';
    }

    /**
     * Creates a vote in the database by the user on the specific post.
     * @param type $userId The id of the voter.
     * @param type $postId The id of the post the vote is for.
     */
    function voteOnPost($userId, $postId)
    {
        $dbHandler = $this->getDbHandler();
        $dbHandler->addRecord("Vote", ["Owner_id", "Post_id", "Vote_date"], [$userId, $postId, date("Y-m-d H:i:s")], [PDO::PARAM_INT, PDO::PARAM_INT, PDO::PARAM_STR]);
    }

    /**
     * Returns the number of votes for a specific post.
     * @param type $postId The id of the post.
     * @return type The number of votes.
     */
    function getVotesForPost($postId)
    {
        $dbHandler = $this->getDbHandler();
        $records = $dbHandler->getRecords("Vote", "Post_id", $postId, PDO::PARAM_INT);
        return count($records);
    }
    
    /**
     * Returns the number of votes that a specific user has used.
     * @param type $userId The id of the user.
     * @return type The number of votes.
     */
    function getVotesFromUser($userId)
    {
        $dbHandler = $this->getDbHandler();
        $records = $dbHandler->getRecords("Vote", "Owner_id", $userId, PDO::PARAM_INT);
        return count($records);
    }
    
    /**
     * Decides if a user can cast a vote by using the voting limit.
     * @param type $userId The id of the user.
     * @return boolean True if the user has not exceeded his voting limit.
     */
    function canVote($userId)
    {
        $dbHandler = $this->getDbHandler();
        $records = $dbHandler->getRecords("Vote", "Owner_id", $userId, PDO::PARAM_INT);
        $votesToday = 0;
        $dateNow = new DateTime();
        for ($i = 0; $i < count($records); $i++)
        {
            $voteDate = new DateTime($records[$i][3]);
            if ($voteDate->format("Y-m-d") === $dateNow->format("Y-m-d"))
            {
                $votesToday++;
            }
        }
        if ($votesToday >= 5)
        {
            return false;
        }
        return true;
    }
    /**
     * Returns the comments for a specific post as "Comment" objects.
     * @param type $postId The id of the post.
     * @return array Array of "Comment" objects.
     */
    function getCommentsForPost($postId)
    {
        $comments = [];
        $dbHandler = $this->getDbHandler();
        $userHandler = new UserHandler();
        $records = $dbHandler->getRecords("Comment", "Post_id", $postId, PDO::PARAM_INT);
        for ($i = 0; $i < count($records); $i++)
        {
            $commentOwner = null;
            $userHandler->getUserById($records[$i][1], $commentOwner);
            array_push($comments, new Comment($records[$i][0], $records[$i][1], $records[$i][2], $records[$i][3], $records[$i][4], $commentOwner->profileImagePath, $commentOwner->nameToShow));
        }
        return $comments;
    }
    
    /**
     * Creates a comment and adds it to the database.
     * @param type $userId The id of the user that made the comment.
     * @param type $postId The id of the post the comment is for.
     * @param type $content The content of the comment.
     */
    function commentOnPost($userId, $postId, $content)
    {
        $dbHandler = $this->getDbHandler();
        $dbHandler->addRecord("Comment", ["Owner_id", "Post_id", "Comment_date", "Content"], [$userId, $postId, date("Y-m-d H:i:s"), $content], [PDO::PARAM_INT, PDO::PARAM_INT, PDO::PARAM_STR, PDO::PARAM_STR]);
    }

    /**
     * Returns the html that displays the comment correctly on the site.
     * @param type $comment The comment to displayu
     * @return type html string that displays the post.
     */
    function getCommentHtml($comment)
    {
        return '<div class="comment">'
                . '<a class="avatar">'
                . '<img src="src/getProfileImage.php?image=' . $comment->profileImagePath . '">'
                . '</a>'
                . '<div class="content">'
                . '<a class="author">' . $comment->ownerName . '</a>'
                . ' <div class="metadata">'
                . ' <span class="date">' . date_format($comment->commentDate, 'd/m/Y H:i') . '</span>'
                . ' </div>'
                . '<div class="text">'
                . $comment->content
                . '</div>'
                . '<div class="actions">'
                . '<a class="reply">Reply</a>'
                . '</div>'
                . '</div>'
                . '</div>';
    }
    
    /**
     * Creates an instance of the database handler using information defined in the 'config.php' file.
     * @return \DatabaseHandler
     */
    private function getDbHandler()
    {
        $dbHandler = new DatabaseHandler(DATABASE_SERVER, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_SHOW_EXCEPTIONS);
        $dbHandler->connectToDb(DATABASE_NAME);
        return $dbHandler;
    }

}
