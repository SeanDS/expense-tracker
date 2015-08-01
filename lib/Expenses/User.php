<?php

namespace Expenses;

use Config;

use PDO;
use Exception;
use DateTime;
use DateTimeZone;
use InvalidArgumentException;

use Expenses\InvalidCredentialsException;

class User extends AbstractSingular
{
    public static $attributeTypes = array(
        'userid'        =>  PDO::PARAM_INT,
        'username'      =>  PDO::PARAM_STR,
        'password'      =>  PDO::PARAM_STR,
        'salt'          =>  PDO::PARAM_STR,
        'dateformat'    =>  PDO::PARAM_STR,
        'lastlogin'     =>  PDO::PARAM_STR
    );
    
    public static $table = 'users';
    public static $idColumn = 'userid';
    
    public static function fromUsername($username) {
        return new self(self::getUserIdFromUsername($username));
    }
    
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
        
        $salt = self::createSalt();
        $password = self::createPasswordHash($data['password'], $salt);
        
        $dateFormat = 'Y-m-d H:i:s';
        
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
    
    public static function login($username, $password) {
        if (! self::checkCredentials($username, $password)) {            
            throw new InvalidCredentialsException();
        }
        
        $user = self::fromUsername($username);
        $user->load();
        
        $user->updateLastLogin();
            
        return $user;
    }
    
    public function updateLastLogin()
    {
        $this->setAttribute('lastlogin', date(DB_DATE_FORMAT), true);
    }
    
    public static function validateUsername($username) {
        $length = strlen($username);
        
        return (
            preg_match('!^[\w -_]*$!', $username) &&
            ($length >= Config::MINIMUM_USERNAME_LENGTH) &&
            ($length <= Config::MAXIMUM_USERNAME_LENGTH) &&
            ($length <= 32) // database-defined maximum length
        );
    }
    
    public static function userExists($username)
    {
        global $db;
        
        if (! self::validateUsername($username)) {
            throw new InvalidArgumentException("Specified username is invalid.");
        }

        $usernameExistsQuery = $db->prepare("
            SELECT EXISTS(SELECT 1 FROM " . Config::TABLE_PREFIX . static::$table . " WHERE username = :username)
        ");

        $usernameExistsQuery->bindParam(':username', $username, PDO::PARAM_STR);
        $usernameExistsQuery->execute();

        $usernameExistsQuery->bindColumn(1, $exists);
        $usernameExistsQuery->fetch();

        return (bool) $exists;
    }
    
    /**
     * Gets userid for given username. Throws an exception if the username
     * doesn't exist.
     * 
     * @global type $db
     * @param type $username
     * @return type
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function getUserIdFromUsername($username) {
        global $db;
        
        if (! self::validateUsername($username)) {
            throw new InvalidArgumentException("Specified username is invalid.");
        }
        
        $userIdQuery = $db->prepare("
            SELECT " . static::$idColumn . "
            FROM " . Config::TABLE_PREFIX . static::$table . "
            WHERE username = :username
        ");
        
        $userIdQuery->bindParam(":username", $username, PDO::PARAM_STR);
        $userIdQuery->execute();
        $userIdQuery->bindColumn(static::$idColumn, $userId, PDO::PARAM_INT);
        $userIdQuery->fetch(PDO::FETCH_BOUND);
        
        if (! self::validateId($userId)) {
            throw new Exception("Specified username does not exist.");
        }
        
        return $userId;
    }
    
    public static function checkCredentials($username, $password) {
        global $db;
        
        $credentialQuery = $db->prepare("
            SELECT EXISTS(
                SELECT 1
                FROM " . Config::TABLE_PREFIX . static::$table . "
                WHERE
                    username = :username AND
                    password = SHA2(CONCAT(salt, :password), 512)
            )
        ");
        
        $credentialQuery->bindParam(":username", $username);
        $credentialQuery->bindParam(":password", $password);
        
        $credentialQuery->execute();
        
        $credentialQuery->bindColumn(1, $exists);
        $credentialQuery->fetch(PDO::FETCH_BOUND);
        
        return (bool) $exists;
    }

    // $nonDescriptiveFormat is only used if $descriptive is false
    public function formatDate($dateString, $descriptive = true) {
        $timestamp = strtotime($dateString);
        
        // TODO: use user timezone
        $timezone = new DateTimeZone('Europe/London');
    
        $now = new DateTime("now", $timezone);
        $time = new DateTime("@" . $timestamp, $timezone);

        if ($descriptive) {
            if($timestamp == 0) {
                return "Never";
            }

            $difference = $now->getTimestamp() - $time->getTimestamp();

            if ($difference >= 0) {
                // In the past

                if ($difference < 3) {
                    return "Now";
                } elseif ($difference < 60) {
                    return ($difference == 1) ? "1 second ago" : $difference . " seconds ago";
                } elseif ($difference < 3600) {
                    $difference = intval(floor($difference / 60));

                    return ($difference == 1) ? "1 minute ago" : $difference . " minutes ago";
                } elseif ($difference < 86400) {
                    $difference = intval(floor($difference / 3600));

                    return ($difference == 1) ? "1 hour ago" : $difference . " hours ago";
                } elseif ($difference < 2592000) {
                    $difference = intval(floor($difference / 86400));

                    return ($difference == 1) ? "1 day ago" : $difference . " days ago";
                } elseif ($difference < 31536000) {
                    $difference = intval(floor($difference / 2592000));

                    return ($difference == 1) ? "1 month ago" : $difference . " months ago";
                } else {
                    $difference = intval(floor($difference / 31536000));

                    return ($difference == 1) ? "1 year ago" : $difference . " years ago";
                }
            } else {
                // Flip sign. This prevents potential issues with rounding (PHP can round upwards on negative numbers at times, where the desired behaviour is to round down)
                $difference = -$difference;

                if ($difference < 60) {
                    return ($difference == 1) ? "in 1 second" : "in " . $difference . " seconds";
                } elseif ($difference < 3600) {
                    $difference = intval(floor($difference / 60));

                    return ($difference == 1) ? "in 1 minute" : "in " . $difference . " minutes";
                } elseif ($difference < 86400) {
                    $difference = intval(floor($difference / 3600));

                    return ($difference == 1) ? "in 1 hour" : "in " . $difference . " hours";
                } elseif ($difference < 2592000) {
                    $difference = intval(floor($difference / 86400));

                    return ($difference == 1) ? "in 1 day" : "in " . $difference . " days";
                } elseif ($difference < 31536000) {
                    $difference = intval(floor($difference / 2592000));

                    return ($difference == 1) ? "in 1 month" : "in " . $difference . " months";
                } else {
                    $difference = intval(floor($difference / 31536000));

                    return ($difference == 1) ? "in 1 year" : "in " . $difference . " years";
                }
            }
        } else {
            return gmdate($this->getAttribute('dateformat'), $timestamp);
        }
    }
    
    /**
     * Sets password, creating a new salt and updating the encrypted email hash (which uses the salt).
     * 
     * @param type $password
     */
    public function setPassword($password)
    {
        /*
         * create new salt
         */
        
        $salt = self::createSalt();
        
        /*
         * update password and salt
         */
        
        $this->setAttribute('password', self::createPasswordHash($password, $salt));
        $this->setAttribute('salt', $salt);
    }

    private static function createSalt()
    {
        // Create salt
        $saltCharacterList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*?";
        $saltUpperBound = strlen($saltCharacterList) - 1;
        $salt = "";

        for($i = 0; $i < 128; $i++)
        {
                $salt .= $saltCharacterList{mt_rand(0, $saltUpperBound)};
        }

        return $salt;
    }

    private static function createPasswordHash($password, $salt)
    {
        return hash('sha512', $salt . $password);
    }
}

?>
