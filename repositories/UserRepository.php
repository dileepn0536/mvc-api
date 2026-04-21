<?php

class UserRepository
{
    public function __construct()
    {
    }

    private function mapToUser($row)
    {
        return new User($row['name'], $row['email'], $row['id']);
    }

    private function db() {
        return Database::getInstance()->getConnection();
    }

    public function getUsers($limit = 20, $offset = 0)
    {
        $stmt = $this->db()->prepare("SELECT * FROM users LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        $users = [];

        while($row = $stmt->fetch()) {
            $users[] = $this->mapToUser($row);
        }

        return $users;
    }

    public function createUser($name,$email)
    {
        $stmt = $this->db()->prepare("INSERT INTO users (`name`,`email`) values (?,?)");
        return $stmt->execute([$name,$email]);
    }

    public function getUserById($id)
    {
        $stmt = $this->db()->prepare("select * from users where id=?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if(!$row) {
            return null;
        }

        return $this->mapToUser($row);
    }

    public function updateUser($id, $name, $email)
    {
        $stmt = $this->db()->prepare("UPDATE users set name=?, email=? where id=?");
        return $stmt->execute([$name,$email,$id]);
    }

    public function deleteUser($id)
    {
        $stmt = $this->db()->prepare("DELETE FROM users where id=?");
        return $stmt->execute([$id]);
    }
}