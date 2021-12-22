<?php
/**
class User
Создаёт экземпляр с данными о человеке. Конструктор класса либо создает человека в БД
с заданной информацией, либо берет информацию из БД по id.
 */
class User
{
    private int $id;
    private string $name;
    private string $surname;
    private string $birthday_date;
    private int $sex;
    private string $city;

    /**
    Создаёт экземпляр с данными о человеке. Принимает данные ввиде ассоциативного массива,
    ключи в массиве: id, name, surname, birthday_date в формате Y-m-d, sex(0 - women,1 - men), city.
    Если функция параметр с ключём id, то она берёт данные из БД для экземпляра.
    Для создания экземпляра с заданными свойствами необходимо предоставить ассоциативный массив
    с ключами name, surname, birthday_date, sex, city.
     */
    public function __construct(array $param)
    {
        if (isset($param['id'])) {
            $id = $param['id'];
            self::validateId($id);
            $sql = "SELECT * FROM users WHERE id = $id";
            $user = DBConnection::$connection->query($sql)->fetch();
        } else {
            $valid_param = self::validateUserParam($param);
            if (count($valid_param) == 5) {
                $valid_param = self::validateUserParam($param);
                $key_arr = array_keys($valid_param);
                $str_key = implode(' , ',$key_arr);
                $str_val = implode('\' , \'',$valid_param);
                $sql = "INSERT INTO users ($str_key) VALUES ('$str_val')";
                print $sql;
                DBConnection::$connection->query($sql);
                $sql = 'SELECT * FROM users WHERE id = last_insert_id()';
                $result = DBConnection::$connection->query($sql);
                if (($user = $result->fetch()) == null) {
                    throw new Exception('Error in adding user');
                }
            } else {
                throw new Exception('Params is not correct');
            }
        }

        $this->id = $user['id'];
        $this->name = $user['name'];
        $this->surname = $user['surname'];
        $this->birthday_date = $user['birthday_date'];
        $this->sex = $user['sex'];
        $this->city = $user['city'];
    }

    public static function validateUserParam(array $param): array
    {
        $valid_param = array();
        if (isset($param['name'])) {
            self::validateName($param['name']);
            $valid_param['name'] = $param['name'];
        };
        if (isset($param['surname'])) {
            self::validateSurname($param['surname']);
            $valid_param['surname'] = $param['surname'];
        };
        if (isset($param['birthday_date'])) {
            self::validateBirthdayDate($param['birthday_date']);
            $valid_param['birthday_date'] = $param['birthday_date'];
        };
        if (isset($param['sex'])) {
            self::validateSex($param['sex']);
            $valid_param['sex'] = $param['sex'];
        };
        if (isset($param['city'])) {
            self::validateCity($param['city']);
            $valid_param['city'] = $param['city'];
        };

        return $valid_param;
    }

    public static function validateId($id)
    {
        if (is_numeric($id)) {
            $sql = "SELECT * FROM users WHERE id = $id";
            $result = DBConnection::$connection->query($sql);
            if ($result->fetch() == null) {
                throw new Exception('User not found');
            }
        } else {
            throw new Exception('ID must be numeric');
        }
    }

    public static function validateName($name)
    {
        if (strlen($name) < 3) {
            throw new Exception('Name must be 3 and more letters');
        }

        return $name;
    }

    public static function validateSurname($surname)
    {
        if (strlen($surname) < 3) {
            throw new Exception('Surname must be 3 and more letters');
        }

        return $surname;
    }

    public static function validateBirthdayDate($birthday_date)
    {
        $date = DateTime::createFromFormat('Y-m-d', $birthday_date);
        if (($date)
            && ($date->format('Y-m-d') == $birthday_date)
            && (self::convertDateToAge($birthday_date) > 3)
        ) {
            return $birthday_date;
        } else {
            throw new Exception('Data format is not correct. Must be Y.m.d');
        }
    }

    public static function validateSex($sex)
    {
        if (!in_array($sex, range(0,1))) {
            throw new Exception('Sex must be 0 or 1');
        }

        return $sex;
    }

    public static function validateCity($city)
    {
        if (strlen($city) < 3) {
            throw new Exception('City must be 3 and more letters');
        }

        return $city;
    }

    public function saveInBD()
    {
        $sql = "UPDATE users"
            . "SET name = '$this->name',surname = '$this->surname',"
            . "birthday_date = '$this->birthday_date',"
            . "sex = '$this->sex',city = '$this->city'"
            . "WHERE id = '$this->id'";
        DBConnection::$connection->query($sql);
    }

    public function deleteById()
    {
        $sql = "DELETE FROM users WHERE id = $this->id";

        return DBConnection::$connection->query($sql);
    }

    public static function convertDateToAge(string $birthday_date)
    {
        $current_date = new DateTime(date('Y-m-d'));
        $age = $current_date->diff(new DateTime($birthday_date));

        return $age->y;
    }

    public static function convertSexToString(int $sex)
    {
        $sex_arr = ['women', 'men'];

        return $sex_arr[$sex];
    }

    public function formatUser()
    {
        $stdClass = new stdClass();
        $stdClass->id = $this->id;
        $stdClass->name = $this->name;
        $stdClass->surname = $this->surname;
        $stdClass->age = self::convertDateToAge($this->birthday_date);
        $stdClass->birthday_date = $this->birthday_date;
        $stdClass->sex = self::convertSexToString($this->sex);
        $stdClass->city = $this->city;

        return $stdClass;
    }
}
?>