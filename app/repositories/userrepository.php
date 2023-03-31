<?php

namespace Repositories;

use Models\User;
use Models\UserType;
use PDO;
use PDOException;
use Repositories\Repository;

class UserRepository extends Repository
{
    function rowToUser($row)
    {
        if($row == null) {
            return null;
        }
        $user = new User();
        $user->id = $row['id'];
        $user->first_name = $row['first_name'];
        $user->last_name = $row['last_name'];
        $user->address = $row['address'];
        $user->phone_number = $row['phone_number'];
        $user->date_of_birth = $row['date_of_birth'];
        $user->gender = $row['gender'];
        $user->email = $row['email'];
        //$user->password = $row['password'];
        $user->user_type_id = $row['user_type_id'];

        $userType = new UserType();
        $userType->id = $row['user_type_id'];
        $userType->name = $row['user_type_name'];
        $user->user_type = $userType;

        $user->password = "";

        return $user;
    }

    public function getAll($limit = null, $offset = null)
    {
        try {
            $query = "SELECT users.*, user_types.name as user_type_name FROM users INNER JOIN user_types ON users.user_type_id=user_types.id";
            if (isset($limit) && isset($offset)) {
                $query .= " LIMIT :limit OFFSET :offset ";
            }
            $stmt = $this->connection->prepare($query);

            if (isset($limit) && isset($offset)) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            }

            $stmt->execute();

            $users = array();
            while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
                $users[] = $this->rowToUser($row);
            }

            return $users;

        } catch (PDOException $e) {
            echo $e;
        }
    }

    public function getOne($id)
    {
        try {
            $stmt = $this->connection->prepare("SELECT users.*, user_types.name as user_type_name FROM users INNER JOIN user_types ON users.user_type_id=user_types.id WHERE users.id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $row = $stmt->fetch();
            $user = $this->rowToUser($row);

            return $user;
        } catch (PDOException $e) {
            echo $e;
        }
    }

    public function insert($user)
    {
        try{
            $stmt = $this->connection->prepare("INSERT INTO users (first_name, last_name, address, phone_number, date_of_birth, gender, email, password, user_type_id)
            VALUES (:first_name, :last_name, :address, :phone_number, :date_of_birth, :gender, :email, :password, :user_type_id)");
            $stmt->bindParam(':first_name', $user->first_name);
            $stmt->bindParam(':last_name', $user->last_name);
            $stmt->bindParam(':address', $user->address);
            $stmt->bindParam(':phone_number', $user->phone_number);
            $stmt->bindParam(':date_of_birth', $user->date_of_birth);
            $stmt->bindParam(':gender', $user->gender);
            $stmt->bindParam(':email', $user->email);

            $hashedPassword = $this->hashPassword($user->password);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':user_type_id', $user->user_type_id);
            $stmt->execute();
            $user->id = $this->connection->lastInsertId();
            return $user;

        }catch (PDOException $e) {
            echo $e;
        }
    }

    public function update($user, $id)
    {
        try {
            $stmt = $this->connection->prepare("UPDATE users SET first_name=:first_name, last_name=:last_name, address=:address, phone_number=:phone_number, date_of_birth=:date_of_birth, gender=:gender, email=:email WHERE id=:id");
            $stmt->bindParam(':first_name', $user->first_name);
            $stmt->bindParam(':last_name', $user->last_name);
            $stmt->bindParam(':address', $user->address);
            $stmt->bindParam(':phone_number', $user->phone_number);
            $stmt->bindParam(':date_of_birth', $user->date_of_birth);
            $stmt->bindParam(':gender', $user->gender);
            $stmt->bindParam(':email', $user->email);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $this->getOne($id);
        }
        catch (PDOException $e) {
            echo $e;
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->connection->prepare("DELETE FROM users WHERE id=:id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
        catch (PDOException $e) {
            echo $e;
        }
    }

    function checkEmailPassword($email, $password)
    {
        try {
            // retrieve the user with the given email
            $stmt = $this->connection->prepare("SELECT users.*, user_types.name as user_type_name FROM users INNER JOIN user_types ON users.user_type_id=user_types.id WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Models\User');
            $user = $stmt->fetch();

            if (!$user)
                return false;

            // verify if the password matches the hash in the database
            $result = $this->verifyPassword($password, $user->password);

            if (!$result)
                return false;

            // do not pass the password hash to the caller
            $user->password = "";

            return $user;
        } catch (PDOException $e) {
            echo $e;
        }
    }

    public function checkEmail($email){
        try{
            $stmt = $this->connection->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Models\User');
            $user = $stmt->fetch();
            if($user){
                return true;
            }
            return false;
        }catch (PDOException $e) {
            echo $e;
        }
    }

    // hash the password (currently uses bcrypt)
    function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    // verify the password hash
    function verifyPassword($input, $hash)
    {
        return password_verify($input, $hash);
    }
}
