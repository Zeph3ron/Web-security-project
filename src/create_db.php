<?php
require_once dirname(__FILE__) . '../../classes/DatabaseHandler.php';
require_once dirname(__FILE__) . '../../config.php';
//This file is used to create the database and the tables used for this project.
//It assumes mysql is already running and uses the values from "config.php".
//NOTE: Any changes to the database structure must also be updated here, and in the models
$dbHandler = new DatabaseHandler(DATABASE_SERVER, DATABASE_USERNAME, DATABASE_PASSWORD, TRUE);
$userColumns = [
    "`Id` int(11) NOT NULL AUTO_INCREMENT",
    "`Username` varchar(50) NOT NULL",
    "`Display_name` varchar(50) DEFAULT NULL",
    "`Email` varchar(50) NOT NULL",
    "`Sex` enum('M','F') DEFAULT NULL",
    "`User_description` varchar(200) DEFAULT NULL",
    "`Password_hash` varchar(60) NOT NULL",
    "`Failed_login_attempts` int(11) NOT NULL DEFAULT '0'",
    "`Last_failed_login` datetime DEFAULT NULL",
    "PRIMARY KEY (`Id`)"];

$postColumns = [
    "`Id` int(11) NOT NULL AUTO_INCREMENT",
    "`Owner_id` int(11) NOT NULL",
    "`Title` varchar(50) DEFAULT NULL",
    "`Post_date` datetime NOT NULL",
    "`Nr_of_votes` int(11) NOT NULL DEFAULT '0'", 
    "`Content` text",
    "PRIMARY KEY (`Id`)",
    "KEY `FK_Post_User_Id` (`Owner_id`)",
    "CONSTRAINT `FK_Post_User_Id` FOREIGN KEY (`Owner_id`) REFERENCES `User` (`Id`)"];

$commentColumns = [
    "`Id` int(11) NOT NULL AUTO_INCREMENT",
    "`Owner_id` int(11) NOT NULL",
    "`Post_id` int(11) NOT NULL",
    "`Comment_date` datetime NOT NULL",
    "`Content` text",
    "PRIMARY KEY (`Id`)",
    "KEY `FK_Comment_User_Id` (`Owner_id`)",
    "KEY `FK_Comment_Post_Id` (`Post_id`)",
    "CONSTRAINT `FK_Comment_Post_Id` FOREIGN KEY (`Post_id`) REFERENCES `Post` (`Id`)",
    "CONSTRAINT `FK_Comment_User_Id` FOREIGN KEY (`Owner_id`) REFERENCES `User` (`Id`)"];

$dbHandler ->createDb(DATABASE_NAME);
$dbHandler ->createTable("User", $userColumns);
$dbHandler ->createTable("Post", $postColumns);
$dbHandler ->createTable("Comment", $commentColumns);

