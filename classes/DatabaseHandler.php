<?php

/**
 * Description of DatabaseHandler
 * Handles all CRUD operations on the database. Is supposed to work with any mysql database.
 */
class DatabaseHandler {

    var $host;
    var $userName;
    var $password;
    var $conn;
    var $showExceptions;

    /**
     * When instantiated, creates a PDO connection object using the provided credentials.
     * Can then be used to handle all database operations.
     * @param type $host The host to connect to. Example: "localhost".
     * @param type $username The username for the mysql account.
     * @param type $password The password for the username.
     * @param type $showExceptions Set this to true if you want the browser to show exceptions.
     */
    public function __construct($host, $username, $password, $showExceptions)
    {
        $this->host = $host;
        $this->userName = $username;
        $this->password = $password;
        $this->showExceptions = $showExceptions;

        $this->conn = new PDO("mysql:host=$host", $username, $password);
        $this->handleExceptions();
    }

    /**
     * Uses the existing PDO connection to create the provided database, then connects to it.
     * @param type $dbName The name of the database in string format.
     */
    function createDb($dbName)
    {
        $sqlCreateDb = "CREATE DATABASE $dbName";
        $this->conn->exec($sqlCreateDb);
        $this->connectToDb($dbName);
    }

    /**
     * Uses the existing PDO connection to delete the provided database.
     * @param type $dbName The name of the database in string format.
     */
    function deleteDb($dbName)
    {
        $dropDatabase = "DROP DATABASE $dbName";
        $this->conn->exec($dropDatabase);
    }

    /**
     * Uses the existing PDO connection to connect to the provided database.
     * @param type $dbName The name of the database in string format.
     */
    function connectToDb($dbName)
    {
        $this->conn = new PDO("mysql:host=$this->host;dbname=$dbName", $this->userName, $this->password);
        $this->handleExceptions();
    }

    /**
     * Uses the existing PDO connection to create a new table. 
     * @param type $tableName he name of the table to create.
     * @param type $columns An array of strings containing the column name in this format "column1 datatype". EXAMPLE: "FirstName VARCHAR(50)".
     */
    function createTable($tableName, $columns)
    {
        $statement = "CREATE TABLE $tableName (";
        for ($i = 0; $i < count($columns); $i++)
        {
            $statement .= $columns[$i];
            if ($i < count($columns) - 1)
            {
                $statement .= ",";
            }
        }
        $statement .= ");";
        $preparedStatement = $this->conn->prepare($statement);
        $preparedStatement->execute();
    }

    /**
     * Inserts some data into an existing table in the database. 
     * @param type $tableName The table to add the data to.
     * @param type $columns An array containing the column names.
     * @param type $values An array containing the values to add.
     * @param type $pdoBindParameters An array containing the datatype parameters for each column. Example: PDO::PARAM_STR.
     */
    function addRecord($tableName, $columns, $values, $pdoBindParameters)
    {
        $sql = "INSERT INTO $tableName (";
        for ($i = 0; $i < count($columns); $i++)
        {
            $sql .= ($columns[$i]);
            $sql .= (($i < count($columns) - 1) ? "," : ") VALUES (");
        }
        for ($i = 0; $i < count($columns); $i++)
        {
            $sql .= ":" . $columns[$i];
            $sql .= ($i < count($columns) - 1) ? "," : ");";
        };
        $stmt = $this->conn->prepare($sql);
        for ($i = 0; $i < count($columns); $i++)
        {
            $column = $columns[$i];
            $value = $values[$i];
            $pdoBindParameter = $pdoBindParameters[$i];
            $stmt->bindValue(":$column", $value, $pdoBindParameter);
        }
        $stmt->execute();
    }

    /**
     * Gets every column (*) from the provided table based on some WHERE condition.
     * @param type $tableName The name of the table.
     * @param type $columnToCheck The name of the column to use in the WHERE clause.
     * @param type $valueToCheck The value to check against.
     * @param type $pdoBindParameter The PDO bind parameter. Example "PDO::PARAM_STR"
     * @return type
     */
    function getRecords($tableName, $columnToCheck, $valueToCheck, $pdoBindParameter)
    {
        $sql = "SELECT * FROM $tableName WHERE $columnToCheck = :$columnToCheck;";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":$columnToCheck", $valueToCheck, $pdoBindParameter);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * 
     * @param type $tableName The name of the table.
     * @return type
     */
    function getAllRecords($tableName)
    {
        $sql = "SELECT * FROM $tableName;";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Updates a record in the database. NOTE: This method is not safe to directly handle user input.
     * @param type $tableName The name of the table.
     * @param type $columnToUpdate The name of the column to update.
     * @param type $newValue The new updated value.
     * @param type $conditionalColumn The column to use in the WHERE clause.
     * @param type $condition The condition to compare against.
     * @param type $pdoBindParameter The PDO bind parameter. Example "PDO::PARAM_STR"
     * @return type
     */
    function updateRecord($tableName, $columnToUpdate, $newValue, $conditionalColumn, $condition, $pdoBindParameter)
    {
        $sql = "UPDATE $tableName SET $columnToUpdate = :newValue WHERE $conditionalColumn = '$condition';";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":newValue", $newValue, $pdoBindParameter);
        $stmt->execute();
    }

    /**
     * Increments a record in the database by 1. NOTE: This method is not safe to directly handle user input.
     * @param type $tableName The name of the table.
     * @param type $columnToIncrement The name of the column to increment.
     * @param type $conditionalColumn The column to use in the WHERE clause.
     * @param type $condition The condition to compare against.
     * @return type
     */
    function incrementRecord($tableName, $columnToIncrement, $conditionalColumn, $condition)
    {
        $sql = "UPDATE $tableName SET $columnToIncrement = $columnToIncrement + 1 WHERE $conditionalColumn = '$condition';";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
    }
    
    /**
     * Deletes a record from the database.
     * @param type $tableName The name of the table.
     * @param type $conditionalColumn The column to use in the WHERE clause.
     * @param type $condition The condition to compare against.
     * @param type $pdoBindParameter The PDO bind parameter. Example "PDO::PARAM_STR"
     */
    function deleteRecord($tableName, $conditionalColumn, $condition, $pdoBindParameter)
    {
        $sql = "DELETE FROM $tableName WHERE $conditionalColumn = :condition";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":condition", $condition, $pdoBindParameter);
        $stmt->execute();
    }

    /**
     * Called every time a new DBO object is instantiated.
     */
    function handleExceptions()
    {
        if ($this->showExceptions)
        {
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

}
