<?php

require_once dirname(__FILE__) . '../UserHandler.php';

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
                if (!strlen($username) >= 8 && !strlen($username) <= 30)
                {
                    array_push($errors, "The username must be between 8 and 30 characters long.");
                }
                else
                {
                    $accHandler = new UserHandler();
                    if ($accHandler->userExists($username))
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
        require_once $_SERVER['DOCUMENT_ROOT'] . '/securimage/securimage.php';
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
                if (strlen($title)< 5 ||strlen($title) > 30)
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
                if (strlen($content)< 5 ||strlen($content) > 200)
                {
                    array_push($errors, "The length of the post must be between 5 and 200 characters.");
                }
            }
        }
        else
        {
            array_push($errors, "The content field must be set.");
        }
    }

}
