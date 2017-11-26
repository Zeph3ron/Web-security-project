<?php

/**
 * Description of Post
 * Represents a post made by users as it is stored in the database.
 * @author User
 */
class Post {

    var $id;
    var $ownerId;
    var $title;
    var $postDate;
    var $nrOfVotes;
    var $content;

    public function __construct($id, $ownerId, $title, $postDate, $nrOfVotes, $content)
    {
        $this->id = $id;
        $this->ownerId = $ownerId;
        $this->title = $title;
        $this->postDate = new DateTime($postDate);
        $this->nrOfVotes = $nrOfVotes;
        $this->content = $content;
    }

}
