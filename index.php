<?php

    require_once 'User.php';
    require_once 'UserList.php';
/**
class DBConnection
Создаёт экземпляр соединения с БД. Для создания экземпляра необходимо имя хоста,
имя БД, имя пользователя и пороль.
*/
class DBConnection
{
    public static $connection;

    public function __construct($host, $db_name, $username, $password)
    {
        self::$connection = new PDO("mysql:host=$host; dbname=$db_name", $username, $password);
        $sql = 'CREATE TABLE IF NOT EXISTS users('
             . 'id INT AUTO_INCREMENT PRIMARY KEY,'
             . 'name VARCHAR(20),'
             . 'surname VARCHAR (20),'
             . 'birthday_date DATE,'
             . 'sex TINYINT(1),'
             . 'city VARCHAR(20))';
        DBConnection::$connection->query($sql);
    }
}

try {
    new DBConnection('tz', 'test', 'root', '');
} catch (PDOException $e) {
    print($e->getMessage());
}
?>
