<?php

/**
 * Description of Post
 * Represents a post made by users as it is stored in the database.
 */
class Post {

    var $id;
    var $ownerId;
    var $title;
    var $postDate;
    var $nrOfVotes;
    var $content;
    
    public function __construct($id, $ownerId, $title, $postDate, $nrOfVotes, $content, $profileImagePath)
    {
        $this->id = $id;
        $this->ownerId = $ownerId;
        $this->title = htmlentities($title);
        $this->postDate = new DateTime($postDate);
        $this->nrOfVotes = $nrOfVotes;
        $this->content = htmlentities($content);
        $this->profileImagePath = $profileImagePath;
    }
}
