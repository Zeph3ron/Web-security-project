<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <script src="scripts/js/jquery-3.2.1.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.13/semantic.min.css"/>
        <link rel="stylesheet" href="scripts/css/semantic.min.css"/>
        <title>Edit profile</title>
        <style>
            body {
                padding: 1em;
            }

            .ui.action.input input[type="file"] {
                display: none;
            }
        </style>
        <script>
            $(function () {
                $("input:text").click(function () {
                    $(this).parent().find("input:file").click();
                });

                $('input:file', '.ui.action.input')
                        .on('change', function (e) {
                            var name = e.target.files[0].name;
                            $('input:text', $(e.target).parent()).val(name);
                        });
            });
        </script>
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
            require_once dirname(__FILE__) . '/classes/UserHandler.php';
            $userHandler = new UserHandler();
            $user_id = $_SESSION['user_id'];
            $userHandler->getUserById($user_id, $user);
        }
        ?>
        <br/>
        <main class="ui page grid container">
            <div class="row">
                <div class="column">
                    <div class="ui message main">
                        <p>Use this page to upload a unique profile image. Just navigate to your photo and click upload.
                            <br/><br/><b>NOTE: You can only have one profile image at a time. If you upload a new image the old one will get deleted.</b></p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="column">
                    <form class="ui form" action="src/uploadImage.php" method="post" enctype="multipart/form-data">
                        <div class="field">
                            <label>Upload profile image</label>
                            <div class="ui action input">
                                <input type="text" placeholder="Path to your profile image (.PNG, .JPEG)" readonly>
                                <input type="file" name="fileToUpload" id="fileToUpload">
                                <div class="ui icon button">
                                    <i class="attach icon"></i>
                                </div>
                            </div>
                        </div>
                        <button class="ui fluid primary button" type="submit" name="submit">Upload</button>
                    </form>
                    <div>
                        <br/>
                        <button class="ui fluid primary button"type="submit" onclick="window.location = 'editProfilePage.php';">Back to edit profile</button>
                    </div>
                </div>
            </div>
        </main> 
    </body>
</html>
