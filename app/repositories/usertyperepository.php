<?php

namespace Repositories;

use PDO;
use PDOException;

class UserTypeRepository extends Repository
{
    function getAll($offset = NULL, $limit = NULL)
    {
        try {
            $query = "SELECT * FROM user_types";
            if (isset($limit) && isset($offset)) {
                $query .= " LIMIT :limit OFFSET :offset ";
            }
            $stmt = $this->connection->prepare($query);
            if (isset($limit) && isset($offset)) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            }
            $stmt->execute();

            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Models\UserType');
            $userTypes = $stmt->fetchAll();

            return $userTypes;
        } catch (PDOException $e) {
            echo $e;
        }
    }

    function getOne($id)
    {
        try {
            $stmt = $this->connection->prepare("SELECT * FROM user_types WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Models\UserType');
            $product = $stmt->fetch();

            return $product;
        } catch (PDOException $e) {
            echo $e;
        }
    }

    function insert($userType)
    {
        try {
            $stmt = $this->connection->prepare("INSERT into user_types (name) VALUES (?)");

            $stmt->execute([$userType->name]);

            $userType->id = $this->connection->lastInsertId();

            return $userType;
        } catch (PDOException $e) {
            echo $e;
        }
    }

    function update($userType, $id)
    {
        try {
            $stmt = $this->connection->prepare("UPDATE user_types SET name = ? WHERE id = ?");

            $stmt->execute([$userType->name, $id]);

            $userType->id = $id;
            return $userType;
        } catch (PDOException $e) {
            echo $e;
        }
    }

    function delete($id)
    {
        try {
            $stmt = $this->connection->prepare("DELETE FROM user_types WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e;
        }
    }
}