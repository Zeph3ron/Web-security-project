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
        if ($this->getUserByUsername($username, $user))
        {
            //If 'lastFailedLogin' is not set, we skip checking for failed attempts.
            if (isset($user->lastFailedLogin))
            {
                $lastFailedModified = new DateTime($user->lastFailedLogin);
                $lastFailedModified->modify('+3 minutes');
                $dateNow = new DateTime();
                //Here we check if a failed attempt has happened within the last 3 minutes
                if ($dateNow > $lastFailedModified)
                {
                    //If not, we reset the failed attempts
                    $this->resetFailedLogins($username, $user);
                }
                if ($user->failedLoginAttempts >= 5)
                {
                    //If it reaches here there have been 5 or more failed attampets within the last 3 minutes
                    array_push($errors, "You have exceeded your login attempts, try again in 3 minutes.");
                    return;
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
                $_SESSION['user_id'] = $user->id;
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
     * Checks to see if a user exists.
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

    /**
     * Checks to see if a display name already exists.
     * @param type $displayName The display name to check for.
     * @param type $userId The id of the user that is attempting to change his display name.
     * @return boolean
     */
    function displayNameExists($displayName, $userId)
    {
        $dbHandler = $this->getDbHandler();
        $users = $dbHandler->getRecords("User", "Display_name", $displayName, PDO::PARAM_STR);
        //NOTE: The user id is used so that if the user is changing his description but does not alter his display name
        //then he wont get a "Already taken" error (since it is him that has already taken it).
        if (count($users) > 0 && $users[0][0] != $userId)
        {
            return true;
        }
        return false;
    }

    /**
     * Checks to see if the user already exists in the database.
     * @param type $username The user's username.
     * @param type $user An OUT variable, if the user exist this will hold the users information from the database.
     * @return boolean Returns true if the user existed, else false.
     */
    function getUserByUsername($username, &$user)
    {
        $dbHandler = $this->getDbHandler();
        $users = $dbHandler->getRecords("User", "Username", $username, PDO::PARAM_STR);
        if (count($users) > 0)
        {
            //NOTE: Adding/editing/removing columns in the database might affect the indexes.
            $dbUserValues = $users[0];
            $nameToShow = $dbUserValues[2];
            if (empty((trim($nameToShow))))
            {
                //If display name is empty we will display username.
                $nameToShow = $dbUserValues[1];
            }
            $user = new User(
                    $dbUserValues[0], $dbUserValues[1], $dbUserValues[2], $dbUserValues[3], $dbUserValues[4], $dbUserValues[5], $dbUserValues[6], $dbUserValues[7], $dbUserValues[8], $dbUserValues[9], $dbUserValues[10], $dbUserValues[11], $nameToShow);
            return true;
        }
        return false;
    }

    /**
     * Attempts to get a user model that represents the user in the database.
     * @param type $user_id The user's id.
     * @param type $user An OUT variable, if the user exist this will hold the users information from the database.
     * @return boolean Returns true if the user existed.
     */
    function getUserById($userId, &$user)
    {
        $dbHandler = $this->getDbHandler();
        $users = $dbHandler->getRecords("User", "Id", $userId, PDO::PARAM_INT);
        if (count($users) > 0)
        {
            //NOTE: Adding/editing/removing columns in the database might affect the indexes.
            $dbUserValues = $users[0];
            $nameToShow = $dbUserValues[2];
            if (empty((trim($nameToShow))))
            {
                //If display name is empty we will display username.
                $nameToShow = $dbUserValues[1];
            }
            $user = new User(
                    $dbUserValues[0], $dbUserValues[1], $dbUserValues[2], $dbUserValues[3], $dbUserValues[4], $dbUserValues[5], $dbUserValues[6], $dbUserValues[7], $dbUserValues[8], $dbUserValues[9], $dbUserValues[10], $dbUserValues[11], $nameToShow);
            return true;
        }
        return false;
    }
    
    /**
     * Updates the user profile with a new display name and description.
     * @param type $userId The id of the user.
     * @param type $newDisplayName The new display name.
     * @param type $newDescription The new description.
     */
    function updateUserProfile($userId, $newDisplayName, $newDescription)
    {
        $dbHandler = $this->getDbHandler();
        $dbHandler->updateRecord("User", "Display_name", $newDisplayName, "Id", $userId, PDO::PARAM_STR);
        $dbHandler->updateRecord("User", "User_description", $newDescription, "Id", $userId, PDO::PARAM_STR);
    }
    
    /**
     * Updates the profile image path on the user table.
     * @param type $userId The id of the user.
     * @param type $newImagePath The new image path.
     */
    function updateUserProfileImage($userId, $newImagePath)
    {
        $dbHandler = $this->getDbHandler();
        $dbHandler->updateRecord("User", "Profile_image_path", $newImagePath, "Id", $userId, PDO::PARAM_STR);
    }

    /**
     * Checks if the user has administration rights.
     * @param type $userId The id of the user.
     * @return boolean Returns true if the user has administration rights.
     */
    function isAdmin($userId)
    {
        $user = null;
        if ($this->getUserById($userId, $user))
        {
            if ($user->displayName)
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Updates the field "Is_banned" on the user table to 'true'.
     * @param type $userId The id of the user.
     */
    function banUser($userId)
    {
        $dbHandler = $this->getDbHandler();
        $dbHandler->updateRecord("User", "Is_banned", true, "Id", $userId, PDO::PARAM_BOOL);
    }

    /**
     * Increments the 'Failed_login_attempts' column by 1.
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
     * @param type $username The user's username.
     */
    private function resetFailedLogins($username, &$user)
    {
        $dbHandler = $this->getDbHandler();
        $dbHandler->updateRecord("User", "Failed_login_attempts", 0, "Username", $username, PDO::PARAM_INT);
        $user -> failedLoginAttempts = 0;
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
