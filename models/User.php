<?php

/**
 * Description of User
 * Represents the user data structure as it is stored in the database.
 * @author Kristinn
 */
class User {

    var $id;
    var $userName;
    var $displayName;
    var $email;
    var $sex;
    var $userDescription;
    var $passwordHash;
    var $failedLoginAttempts;
    var $lastFailedLogin;

    public function __construct($id, $userName, $displayName, $email, $sex, $userDescription, $passwordHash, $failedLoginAttempts, $lastFailedLogin)
    {
        $this->id = $id;
        $this->userName = $userName;
        $this->displayName = $displayName;
        $this->email = $email;
        $this->sex = $sex;
        $this->userDescription = $userDescription;
        $this->passwordHash = $passwordHash;
        $this->failedLoginAttempts = $failedLoginAttempts;
        $this->lastFailedLogin = $lastFailedLogin;
    }

}