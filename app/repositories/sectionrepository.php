<?php

namespace Repositories;

use PDO;
use PDOException;

class SectionRepository extends Repository
{
    function getAll($sort = NULL, $offset = NULL, $limit = NULL)
    {
        try {
            $query = "SELECT * FROM clinic_sections";
            if(isset($sort) && $sort=='name') {
                $query .= " ORDER BY name";
            }

            if (isset($limit) && isset($offset)) {
                $query .= " LIMIT :limit OFFSET :offset ";
            }

            $stmt = $this->connection->prepare($query);

            if (isset($limit) && isset($offset)) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            }

            $stmt->execute();

            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Models\Section');
            $sections = $stmt->fetchAll();

            return $sections;
        } catch (PDOException $e) {
            echo $e;
        }
    }

    public function getOne($id)
    {
        try {
            $stmt = $this->connection->prepare("SELECT * FROM clinic_sections WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Models\Section');
            $section = $stmt->fetch();

            return $section;
        } catch (PDOException $e) {
            echo $e;
        }
    }

    public function insert($section)
    {
        try {
            $stmt = $this->connection->prepare("INSERT into clinic_sections (name) VALUES (?)");

            $stmt->execute([$section->name]);

            $section->id = $this->connection->lastInsertId();

            return $section;
        } catch (PDOException $e) {
            echo $e;
        }
    }

    public function update($section, $id)
    {
        try {
            $stmt = $this->connection->prepare("UPDATE clinic_sections SET name = ? WHERE id = ?");

            $stmt->execute([$section->name, $id]);

            $section->id = $id;
            return $section;
        } catch (PDOException $e) {
            echo $e;
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->connection->prepare("DELETE FROM clinic_sections WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e;
        }
    }
}