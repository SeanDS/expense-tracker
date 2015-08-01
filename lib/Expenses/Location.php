<?php

namespace Expenses;

use Config;

use PDO;
use Exception;
use InvalidArgumentException;

use Expenses\InvalidCredentialsException;

class Location extends AbstractSingular
{
    public static $attributeTypes = array(
        'locationid'    =>  PDO::PARAM_INT,
        'organisation'  =>  PDO::PARAM_STR,
        'address'       =>  PDO::PARAM_STR
    );
    
    public static $table = 'locations';
    public static $idColumn = 'locationid';
    
    public static function create($data) {
        global $db;
        
        if (! (array_key_exists('username', $data) && (array_key_exists('password', $data)))) {
            throw new InvalidArgumentException("Specified data array must include username and password keys.");
        }
        
        if (! self::validateUsername($data['username'])) {
            throw new Exception("Invalid username specified.");
        }
        
        if (self::userExists($data['username'])) {
            throw new Exception("Specified username is already in use.");
        }
        
        $newUserQuery = $db->prepare("
            INSERT INTO " . Config::TABLE_PREFIX . "users (username, password, salt, dateformat, lastlogin)
            VALUES (:username, :password, :salt, NOW())
        ");
        
        $newUserQuery->bindParam(':username', $data['username'], self::$attributeTypes['username']);
        $newUserQuery->bindParam(':password', $password, self::$attributeTypes['password']);
        $newUserQuery->bindParam(':salt', $salt, self::$attributeTypes['salt']);
        $newUserQuery->bindParam(':dateformat', $dateFormat, self::$attributeTypes['dateformat']);
        
        $newUserQuery->execute();
        
        if (! $newUserQuery->rowCount() === 1) {
            throw new Exception("Database entry not inserted.");
        }
        
        $userId = $db->lastInsertId();
        
        $user = new self($userId);
        $user->load();
        
        return $user;
    }
    
    public function getAddress() {
        return $this->getAttribute('organisation') . ", " . $this->getAttribute('address');
    }
}

?>
