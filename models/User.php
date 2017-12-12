<?php

/**
 * Description of User
 * Represents the user data structure as it is stored in the database.
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
    var $isAdmin;
    var $isBanned;
    var $profileImagePath;
    var $nameToShow;

    public function __construct($id, $userName, $displayName, $email, $sex, $userDescription, $passwordHash, $failedLoginAttempts, $lastFailedLogin, $isAdmin, $isBanned, $profileImagePath, $nameToShow)
    {
        $this->id = $id;
        $this->userName = htmlentities($userName);
        $this->displayName = htmlentities($displayName);
        $this->email = htmlentities($email);
        $this->sex = $sex;
        $this->userDescription = htmlentities($userDescription);
        $this->passwordHash = $passwordHash;
        $this->failedLoginAttempts = $failedLoginAttempts;
        $this->lastFailedLogin = $lastFailedLogin;
        $this->isAdmin = $isAdmin;
        $this->isBanned = $isBanned;
        $this->profileImagePath = $profileImagePath;
        $this->nameToShow = htmlentities($nameToShow);
    }
}
