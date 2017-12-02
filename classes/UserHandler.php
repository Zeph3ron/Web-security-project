<?php

require_once dirname(__FILE__) . '/DatabaseHandler.php';
require_once dirname(__FILE__) . '../../models/User.php';
require_once dirname(__FILE__) . '../../config.php';

/**
 * Description of UserHandler
 * Handles all operations regarding user accounts. Things like Login, creating users, checking to see if the user exists, etc.
 * It uses the Database handler to do most of its operations.
 */
class UserHandler {

    /**
     * Creates an account for the user using the provided information.
     * @param type $username The username to create.
     * @param type $email The email of the user.
     * @param type $password The password in plain-text.
     */
    function createUser($username, $email, $password)
    {
        $dbHandler = $this->getDbHandler();
        $dbHandler->addRecord("User", ["Username", "Email", "Password_hash"], [$username, $email, password_hash($password, PASSWORD_BCRYPT)], [PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR]);
    }

    /**
     * Attempts to login using the provided user credentials. 
     * @param type $username The user's username.
     * @param type $password The password in plain-text.
     * @param type $errors An empty array used to return potential errors.
     */
    function loginUser($username, $password, &$errors)
    {
        $user = null;
        //If 'userExistsComplex' returns true, the '$user' variable
        //will contain the user information from the database.
        if ($this->getUserByName($username, $user))
        {
            //If 'lastFailedLogin' is not set, we skip checking for failed attempts.
            if (isset($user->lastFailedLogin))
            {
                $lastFailedModified = new DateTime($user->lastFailedLogin);
                $lastFailedModified->modify('+3 minutes');
                $dateNow = new DateTime();
                if ($user->failedLoginAttempts >= 5)
                {
                    //Here we check if a failed attempt has happened within the last 3 minutes
                    if ($dateNow > $lastFailedModified)
                    {
                        //If not, we reset the failed attempts
                        $this->resetFailedLogins($username);
                    }
                    else
                    {
                        //If it reaches here there have been 5 or more failed attampets within the last 3 minutes
                        array_push($errors, "You have exceeded your login attempts, try again in 3 minutes.");
                        return;
                    }
                }
            }
            //If it reaches here we proceed to login normally
            if (!$this->attemptLogin($password, $user->passwordHash, $errors))
            {
                $this->incrementFailedLogins($username);
            }
            else
            {
                session_start();
                $_SESSION['user_id'] = $user-> id;
            }
        }
        else
        {
            array_push($errors, "Username doesnt exist.");
        }
    }

    /**
     * Attempts the login by using 'password_verify' to verify the password against the saved hash.
     * @param type $password The password in plain-text.
     * @param type $passwordHash The hashed password, gotten from the database.
     * @param type $errors An array used to return potential errors.
     * @return boolean Returns true if the login succeeded, else false.
     */
    function attemptLogin($password, $passwordHash, &$errors)
    {
        if (!password_verify($password, $passwordHash))
        {
            array_push($errors, "Username and/or password are incorrect.");
            return false;
        }
        return true;
    }

    /**
     * Used to check if a user exists.
     * @param type $username The user's username.
     * @return boolean
     */
    function userExists($username)
    {
        $dbHandler = $this->getDbHandler();
        $users = $dbHandler->getRecords("User", "Username", $username, PDO::PARAM_STR);
        if (count($users) > 0)
        {
            return true;
        }
        return false;
    }
    
    function displayNameExists($displayName, $userId)
    {
        $dbHandler = $this->getDbHandler();
        
        $users = $dbHandler->getRecords("User", "Display_name", $displayName, PDO::PARAM_STR);
        if (count($users) > 0 && $users[0][0] != $userId)
        {
            return true;
        }
        return false;
    }

    /**
     * Checks to see if the user already exists in the database.
     * @param type $dbHandler The DatabaseHandler instance to use.
     * @param type $username The user's username.
     * @param type $user An OUT variable, if the user exist this will hold the users information from the database.
     * @return boolean Returns true if the user existed, else false.
     */
    function getUserByName($username, &$user)
    {
        $dbHandler = $this->getDbHandler();
        $users = $dbHandler->getRecords("User", "Username", $username, PDO::PARAM_STR);
        if (count($users) > 0)
        {
            //NOTE: Adding/editing/removing columns in the database might affect the indexes.
            $dbUserValues = $users[0];
            $user = new User(
                    $dbUserValues[0], $dbUserValues[1], $dbUserValues[2], $dbUserValues[3], $dbUserValues[4], $dbUserValues[5], $dbUserValues[6], $dbUserValues[7], $dbUserValues[8]);
            return true;
        }
        return false;
    }

    /**
     * Attempts to get a user model that represents the user in the database.
     * @param type $user_id The user's id.
     * @param type $user An OUT variable, if the user exist this will hold the users information from the database.
     * @return boolean Returns true if the user existed, else false.
     */
    function getUserById($user_id, &$user)
    {
        $dbHandler = $this->getDbHandler();
        $users = $dbHandler->getRecords("User", "Id", $user_id, PDO::PARAM_INT);
        if (count($users) > 0)
        {
            //NOTE: Adding/editing/removing columns in the database might affect the indexes.
            $dbUserValues = $users[0];
            $user = new User(
                    $dbUserValues[0], $dbUserValues[1], $dbUserValues[2], $dbUserValues[3], $dbUserValues[4], $dbUserValues[5], $dbUserValues[6], $dbUserValues[7], $dbUserValues[8]);
            return true;
        }
        return false;
    }
    
    function updateUserProfile($userId, $newDisplayName, $newDescription)
    {
        $dbHandler = $this->getDbHandler();
        $dbHandler->updateRecord("User", "Display_name", $newDisplayName, "Id", $userId, PDO::PARAM_STR);
        $dbHandler->updateRecord("User", "User_description", $newDescription, "Id", $userId, PDO::PARAM_STR);
    }

    /**
     * Increments the 'Failed_login_attempts' column by 1.
     * @param type $dbHandler The database handler instance to use.
     * @param type $username The user's username.
     */
    private function incrementFailedLogins($username)
    {
        $dbHandler = $this->getDbHandler();
        $dbHandler->incrementRecord("User", "Failed_login_attempts", "Username", $username);
        $dbHandler->updateRecord("User", "Last_failed_login", date("Y-m-d H:i:s"), "Username", $username, PDO::PARAM_STR);
    }

    /**
     * Resets the 'Failed_login_attempts' column to 0.
     * @param type $dbHandler The database handler instance to use.
     * @param type $username The user's username.
     */
    private function resetFailedLogins($username)
    {
        $dbHandler = $this->getDbHandler();
        $dbHandler->updateRecord("User", "Failed_login_attempts", 0, "Username", $username, PDO::PARAM_INT);
    }   

    /**
     * Creates an instance of the database handler using information defined in the 'config.php' file.
     * @return \DatabaseHandler
     */
    private function getDbHandler()
    {
        $dbHandler = new DatabaseHandler(DATABASE_SERVER, DATABASE_USERNAME, DATABASE_PASSWORD, DATABASE_SHOW_EXCEPTIONS);
        $dbHandler->connectToDb(DATABASE_NAME);
        return $dbHandler;
    }

}
