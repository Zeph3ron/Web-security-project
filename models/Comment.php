<?php

/**
 * Description of Comment
 * Represents a comment made by users as it is stored in the database.
 */
class Comment {
    var $id;
    var $ownerId;
    var $postId;
    var $commentDate;
    var $content;
    var $profileImagePath;
    var $ownerName;

    public function __construct($id, $ownerId, $postId, $commentDate, $content, $profileImagePath, $ownerName)
    {
        $this->id = $id;
        $this->ownerId = $ownerId;
        $this->postId = $postId;
        $this->commentDate = new DateTime($commentDate);
        $this->content = htmlentities($content);
        $this->profileImagePath = $profileImagePath;
        $this->ownerName = $ownerName;
    }
}
