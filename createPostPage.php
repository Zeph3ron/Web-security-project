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
        <main class="ui page grid">
            <div class="row">
                <div class="column">
                    <div class="ui message main">
                        <p>Here you can create your own post. Just enter a title and the posts content and press submit.</p>
                    </div>
                </div>
            </div>
            <form class="ui form" action="src/createPost.php" method="post">
                <div class="field">
                    <label>Title</label>
                    <input name="Title" type="text" placeholder="Title"/>
                </div>
                <div class="field">
                    <label>Post content</label>
                    <textarea name="Content" placeholder="Content"></textarea>
                </div>
                <button class="ui fluid primary button" type="submit">Submit</button>
            </form>
        </main>
    </body>
</html>
