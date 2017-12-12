<?php

require_once dirname(__FILE__) . '/UserHandler.php';

/**
 * Description of ValidationHandler
 * Handles all validation operations, like validating username, passwords, email, etc.
 */
class ValidationHandler {
    /**
     * Validates the username according to our defined validation rules.
     * @param type $username The username to validate.
     * @param type $errors An OUT array parameter for all errors that might occur during the validation.
     */
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

    /**
     * Validates that the provided email is in the correct format.
     * @param type $email The email to validate.
     * @param type $errors An OUT array parameter for all errors that might occur during the validation.
     */
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

    /**
     * Validates that the two provided passwords are the same. Used for password confirmation.
     * @param type $password1
     * @param type $password2
     * @param type $errors An OUT array parameter for all errors that might occur during the validation.
     */
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

    /**
     * Validates the captcha code provided by the user against the one stored in the session. 
     * Done automatically by "->check($captchaCode)" method.
     * @param type $captchaCode The code that the user provided.
     * @param type $errors An OUT array parameter for all errors that might occur during the validation.
     */
    function validateCaptcha($captchaCode, &$errors)
    {
        require_once dirname(__FILE__) . '/../securimage/securimage.php';
        $image = new Securimage();
        if ($image->check($captchaCode) != true)
        {
            array_push($errors, "The captcha code was incorrect.");
        }
    }

    /**
     * General validation for a field, makes sure it is set and not empty.
     * @param type $field The field value to validate.
     * @param type $fieldname The name of the field.
     * @param type $errors An OUT array parameter for all errors that might occur during the validation.
     */
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

    /**
     * Validates a post title according to our decided validation rules.
     * @param type $title The title to validate.
     * @param type $errors An OUT array parameter for all errors that might occur during the validation.
     */
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

    /**
     * Validates a post content according to our decided validation rules.
     * @param type $content The content to validate.
     * @param type $errors An OUT array parameter for all errors that might occur during the validation.
     */
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

    /**
     * Validates the user description according to our decided validation rules.
     * @param type $description The description to validate.
     * @param type $errors An OUT array parameter for all errors that might occur during the validation.
     * @return type
     */
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

    /**
     * Validates the users display name according to our decided validation rules. Also checks if it exists.
     * @param type $displayName The display name to validate.
     * @param type $userId The user id.
     * @param type $errors An OUT array parameter for all errors that might occur during the validation.
     */
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

    /**
     * Validates that the provided file is an image. Only allows 'jpg', 'png' and 'gif' extensions and size below '500000'. 
     * @param type $image The file to check. Should be an image.
     * @param type $targetImage The path to the newly uploaded file, if it gets uploaded.
     * @param type $imageFileType The extension type.
     * @param type $errors An OUT array parameter for all errors that might occur during the validation.
     * @return type
     */
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
            array_push($errors, "A image with that randomly generated name already exists, its against all odds! just try again and it will work.");
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

    /**
     * Validates the content of a comment according to our decided validation rules.
     * @param type $content The content of the comment to validate.
     * @param type $errors An OUT array parameter for all errors that might occur during the validation.
     */
    function validateComment($content, &$errors)
    {
        if (isset($content) && is_string($content))
        {
            if (strlen($content) > 200)
            {
                array_push($errors, "The length of the comment cannot exceed 200 characters.");
            }
        }
        else
        {
            array_push($errors, "The comment field must have some content.");
        }
    }

    /**
     * Validates the CSRF token that the user sends with his POST requests against the one kept in the "$_SESSION" variable.
     * @param type $token The token that the user sent.
     * @param type $valErrors An OUT array parameter for all errors that might occur during the validation.
     */
    function validateToken($token, &$valErrors)
    {
        if (!empty($token))
        {
            if (!hash_equals($_SESSION['token'], $token))
            {
                array_push($valErrors, "The validation token was wrong.");
            }
        }
        else
        {
            //Regenerates the token if the validaiton succeded, so that it can only be used once
            if (function_exists('mcrypt_create_iv'))
            {
                $_SESSION['token'] = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM));
            }
            else
            {
                $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(32));
            }
        }
    }
}
