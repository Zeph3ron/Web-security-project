<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <script src="scripts/js/jquery-3.2.1.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.13/semantic.min.css"/>
        <link rel="stylesheet" href="scripts/css/semantic.min.css"/>
        <title>Post page</title>
    </head>
    <body>
        <?php
        session_start();
        if (!$_SESSION['authenticated'] === true)
        {
            header('location:loginPage.php');
        }
        else
        {
            if (!isset($_GET['post']) || $_GET['post'] === '')
            {
                header('location:mainWallPage.php');
            }
            else
            {
                require_once dirname(__FILE__) . '../classes/PostHandler.php';
                require_once dirname(__FILE__) . '../classes/UserHandler.php';
                $postId = $_GET['post'];
                $postHandler = new PostHandler();
                $userHandler = new UserHandler();

                $post = $postHandler->getPost($postId);
                if ($post === null)
                {
                    header('location:mainWallPage.php');
                }
                else
                {
                    $userHandler->getUserById($post->ownerId, $postOwner);
                    $nrOfPosts = $postHandler->getNrOfPosts($postOwner->id);
                }
            }
        }
        ?>
        <br/>
        <main class="ui page grid">
            <div class="row">
                <div class="column">
                    <div class="ui cards">
                        <div class="card">
                            <div class="content">
                                <img class="right floated mini ui image" src="resources/images/bill-small.png">
                                <div class="header">
                                    <?php echo $postOwner->userName ?>
                                </div>
                                <div class="description">
                                    <?php echo $postOwner->userDescription ?>
                                </div>
                                <div class="meta">
                                    <br/>
                                    Has <?php echo $nrOfPosts ?> posts. 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="column">
                    <div class="ui message main">
                        <h1 class="ui header"><?php echo $post->title ?></h1>
                        <p><?php echo $post->content ?>.</p>
                    </div>
                </div>
            </div>
            <div class="ui middle aligned divided list">
            </div>
        </main>
    </body>
</html>
