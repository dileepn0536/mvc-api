<?php

class UserRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getUsers()
    {
        $stmt = $this->db->query("SELECT * FROM users");

        $users = [];

        while($row = $stmt->fetch()) {
            $users[] = new User($row['name'], $row['email'], $row['id']);
        }

        return $users;
    }

    public function createUser($name,$email)
    {
        $stmt = $this->db->prepare("INSERT INTO users (`name`,`email`) values (?,?)");
        return $stmt->execute([$name,$email]);
    }

    public function getUserById($id)
    {
        $stmt = $this->db->prepare("select * from users where id=?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if(!$row) {
            return null;
        }

        return new User($row['name'], $row['email'], $row['id']);
    }

    public function updateUser($id, $name, $email)
    {
        $stmt = $this->db->prepare("UPDATE users set name=?, email=? where id=?");
        return $stmt->execute([$name,$email,$id]);
    }

    public function deleteUser($id)
    {
        $stmt = $this->db->prepare("DELETE FROM users where id=?");
        return $stmt->execute([$id]);
    }
}