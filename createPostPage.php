<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <script src="scripts/js/jquery-3.2.1.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.13/semantic.min.css"/>
        <link rel="stylesheet" href="scripts/css/semantic.min.css"/>
        <title>Create/edit post</title>
    </head>
    <body>
        <?php
        session_start();
        if (!$_SESSION['authenticated'] === true)
        {
            header('location:loginPage.php');
        }
        ?>
        <br/>
        <main class="ui page grid container">
            <div class="row">
                <div class="column">
                    <div class="ui message main">
                        <p>Here you can create your own post. Just enter a title and the posts content and press submit.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="column">
                    <form class="ui form" action="src/createPost.php" method="post">
                        <div class="field">
                            <label>Title</label>
                            <input name="Title" type="text" placeholder="Title" pattern=".{5,30}" required="required" title="Should be between 5 and 30 characters."/>
                        </div>
                        <div class="field">
                            <label>Post content</label>
                            <textarea name="Content" placeholder="Content" required="required" minlength="5" maxlength="1600"></textarea>
                        </div>
                        <button class="ui fluid primary button" type="submit">Submit</button>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="column">
                    <form action="mainWallPage.php" method="post">
                        <button class="ui fluid primary button"type="submit">Back to posts</button>
                    </form>
                </div>
            </div>
        </main>
    </body>
</html>
