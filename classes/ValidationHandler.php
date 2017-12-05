<?php

require_once dirname(__FILE__) . '/UserHandler.php';

/**
 * Description of ValidationHandler
 * Handles all validation operations, like validating username, passwords, email, etc.
 */
class ValidationHandler {

    function validateUsername($username, &$errors)
    {
        if (isset($username))
        {
            if (!is_string($username) || $username === '')
            {
                array_push($errors, "The username field cannot be empty.");
            }
            else
            {
                if (!strlen($username) >= 5 && !strlen($username) <= 30)
                {
                    array_push($errors, "The username must be between 5 and 30 characters long.");
                }
                else
                {
                    $userHandler = new UserHandler();
                    if ($userHandler->userExists($username))
                    {
                        array_push($errors, "This username already exists.");
                    }
                }
            }
        }
        else
        {
            array_push($errors, "The username field must be set.");
        }
    }

    function validateEmail($email, &$errors)
    {
        if (isset($email))
        {
            if (!is_string($email) || $email === '')
            {
                array_push($errors, "The email field cannot be empty.");
            }
            else
            {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                {
                    array_push($errors, "The email must be in valid email form. Example: email@email.com.");
                }
            }
        }
        else
        {
            array_push($errors, "The email field must be set.");
        }
    }

    function validatePasswords($password1, $password2, &$errors)
    {
        if (!isset($password1) || $password1 === '')
        {
            array_push($errors, "You need to select a password.");
        }
        else
        {
            //Only gets here if "password1" was set and not empty
            if (!isset($password2) || $password2 === '')
            {
                array_push($errors, "Your passwords do not match.");
            }
            else
            {
                if ($password1 != $password2)
                {
                    array_push($errors, "Your passwords do not match.");
                }
            }
        }
    }

    function validateCaptcha($captchaCode, &$errors)
    {
        require_once dirname(__FILE__) . '/../securimage/securimage.php';
        $image = new Securimage();
        if ($image->check($captchaCode) != true)
        {
            array_push($errors, "The captcha code was incorrect.");
        }
    }

    function validateField($field, $fieldname, &$errors)
    {
        if (isset($field))
        {
            if (!is_string($field) || $field === '')
            {
                array_push($errors, "The " . $fieldname . " field cannot be empty.");
            }
        }
        else
        {
            array_push($errors, "The " . $fieldname . " field must be set.");
        }
    }

    function validatePostTitle($title, &$errors)
    {
        if (isset($title))
        {
            if (!is_string($title) || $title === '')
            {
                array_push($errors, "The title cannot be empty.");
            }
            else
            {
                if (strlen($title) < 5 || strlen($title) > 30)
                {
                    array_push($errors, "The title length must be between 5 and 30 characters.");
                }
            }
        }
        else
        {
            array_push($errors, "The title field must be set.");
        }
    }

    function validatePostContent($content, &$errors)
    {
        if (isset($content))
        {
            if (!is_string($content) || $content === '')
            {
                array_push($errors, "The content of the post cannot be empty.");
            }
            else
            {
                if (strlen($content) < 5 || strlen($content) > 1600)
                {
                    array_push($errors, "The length of the post must be between 5 and 1600 characters.");
                }
            }
        }
        else
        {
            array_push($errors, "The content field must be set.");
        }
    }

    function validateUserDescription($description, &$errors)
    {
        if (isset($description))
        {
            if (!is_string($description) || $description === '')
            {
                return;
            }
            else
            {
                if (strlen($description) > 200)
                {
                    array_push($errors, "The length of your profile description cannot exceed 200 characters.");
                }
            }
        }
    }

    function validateDisplayName($displayName, $userId, &$errors)
    {
        if (isset($displayName))
        {
            if (!is_string($displayName) || $displayName === '')
            {
                return;
            }
            else
            {
                if (strlen($displayName) < 5 || strlen($displayName) > 30)
                {
                    array_push($errors, "The length of your profile display name should be between 5 and 30 characters.");
                }
                else
                {
                    $userHandler = new UserHandler();
                    if ($userHandler->displayNameExists($displayName, $userId))
                    {
                        array_push($errors, "This display name is already taken.");
                    }
                }
            }
        }
    }

    function validateImage($image, $targetImage, $imageFileType, &$errors)
    {
        //Higly inspired by the code from this link: https://www.w3schools.com/php/php_file_upload.asp
        if (isset($image) && $image != '')
        {
            $check = getimagesize($image);
            if ($check === false)
            {
                array_push($errors, "The file was not an image.");
                return;
            }
        }
        else
        {
            array_push($errors, "No image file was selected.");
            return;
        }

        if (file_exists($targetImage))
        {
            array_push($errors, "This image file already exists.");
        }
        if ($_FILES["fileToUpload"]["size"] > 500000)
        {
            array_push($errors, "Sorry, your image is too large.");
        }
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif")
        {
            array_push($errors, "Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
        }
    }

}
