<?php

session_start();
if (!$_SESSION['authenticated'] === true)
{
    header('location:../loginPage.php');
}
else
{
    $image = $_GET["image"];
    $file = "../resources/images/" . $image;
    $extensionType = pathinfo($image, PATHINFO_EXTENSION);
    if (($extensionType == 'png' || $extensionType == 'jpg' || $extensionType == 'gif') && file_exists($file))
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        header("Content-type: " . finfo_file($finfo, $file));
        finfo_close($finfo);
        $handle = fopen($file, "r");
        while (!feof($handle))
        {
            @$contents .= fread($handle, 8192);
        }
        echo $contents;
    }
    else
    {
        echo '<html><head>'
        . '<title>404 Not Found</title>'
        . '</head><body>'
        . '<h1>Not Found</h1>'
        . '<p>The requested URL was not found on this server.</p>'
        . '<hr>'
        . '<address>Apache/2.4.18 (Ubuntu) Server at 207.154.211.248 Port 80</address>'
        . '</body></html>';
    }
}
?>