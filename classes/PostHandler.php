<?php

require_once dirname(__FILE__). '../DatabaseHandler.php';
require_once dirname(__FILE__) . '../../models/Post.php';
require_once dirname(__FILE__) . '../../config.php';

/**
 * Description of PostHandler
 * Handles all operations regarding user posts.
 */
class PostHandler {

    function getAllPosts()
    {
        $posts = [];
        $dbHandler = $this->getDbHandler();
        $records = $dbHandler->getAllRecords("Post");
        for ($i = 0; $i < count($records); $i++)
        {
            array_push($posts, new Post($records[$i][0], $records[$i][1], $records[$i][2], $records[$i][3], $records[$i][4], $records[$i][5]));
        }
    }

    function createPost($ownerId, $title, $content)
    {
        $dbHandler = $this-> getDbHandler();
        $dbHandler-> addRecord("Post", ["Owner_id", "Title", "Post_date", "Content"], [$ownerId, $title, date("Y-m-d H:i:s"), $content], [PDO::PARAM_INT, PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR]);
    }

    private function getDbHandler()
    {
        $dbHandler = new DatabaseHandler(DATABASE_SERVER, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_SHOW_EXCEPTIONS);
        $dbHandler->connectToDb(DATABASE_NAME);
        return $dbHandler;
    }
}
