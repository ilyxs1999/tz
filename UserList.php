<?php

if (class_exists('User')) {
    class UserList
    {
        private array $id_arr;

        public function __construct(int $id, string $expression)
        {
            $sql = 'SELECT id FROM users WHERE' . ' ';
            if (($expression == '<')
                || ($expression == '>')
                || ($expression == '!=')
            ) {
                $sql.="id $expression $id";
            } else {
                throw new Exception('Expression is not correct');
            }
            $result = DBConnection::$connection->query($sql);
            $ids = $result->fetchAll(PDO::FETCH_COLUMN);
            if (count($ids) > 0) {
                $this->id_arr = $ids;
            } else {
                throw new Exception('People not found');
            }
        }

        public function getUsersById()
        {
            $users = [];
            foreach ($this->id_arr as $id) {
                $users[] = new User(['id' => $id]);
            }

            return $users;
        }

        public function removeUsers()
        {
            $users = $this->getUsersById();
            foreach ($users as $user) {
                $user->deleteById();
            }
        }
    }
} else {
    throw new Exception('User class is not exists');
}
?>

