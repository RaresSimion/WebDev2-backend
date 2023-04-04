<?php

namespace Repositories;

use Models\Doctor;
use Models\Section;
use PDO;
use PDOException;

class DoctorRepository extends Repository
{
    function rowToDoctor($row)
    {
        if($row == null) {
            return null;
        }
        $doctor = new Doctor();
        $doctor->id = $row['id'];
        $doctor->name = $row['name'];
        $doctor->section_id = $row['section_id'];
        $doctor->email = $row['email'];
        $doctor->date_of_birth = $row['date_of_birth'];
        $doctor->phone_number = $row['phone_number'];
        $section = new Section();
        $section->id = $row['section_id'];
        $section->name = $row['section_name'];
        $doctor->section = $section;

        return $doctor;
    }
    public function getAll($offset = NULL, $limit = NULL, $section = NULL)
    {
        try {
            $query = "SELECT doctors.*, clinic_sections.name as section_name FROM doctors INNER JOIN clinic_sections ON doctors.section_id=clinic_sections.id";
            if(isset($section)) {
                $query .= " WHERE doctors.section_id = :section";
            }

            if (isset($limit) && isset($offset)) {
                $query .= " LIMIT :limit OFFSET :offset ";
            }

            $stmt = $this->connection->prepare($query);
            if (isset($section)) {
                $stmt->bindParam(':section', $section);
            }

            if (isset($limit) && isset($offset)) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            }

            $stmt->execute();

            $doctors = array();
            while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
                $doctors[] = $this->rowToDoctor($row);
            }

            return $doctors;
        } catch (PDOException $e) {
            echo $e;
        }
    }

    public function getOne($id)
    {
        try {
            $stmt = $this->connection->prepare("SELECT doctors.*, clinic_sections.name as section_name FROM doctors INNER JOIN clinic_sections ON doctors.section_id=clinic_sections.id WHERE doctors.id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $doctor = $this->rowToDoctor($row);

            return $doctor;
        } catch (PDOException $e) {
            echo $e;
        }
    }

    public function insert($doctor)
    {
        try {
            $stmt = $this->connection->prepare("INSERT INTO doctors (name, email, date_of_birth, phone_number, section_id) VALUES (:name, :email, :date_of_birth, :phone_number, :section_id)");
            $stmt->bindParam(':name', $doctor->name);
            $stmt->bindParam(':email', $doctor->email);
            $stmt->bindParam(':date_of_birth', $doctor->date_of_birth);
            $stmt->bindParam(':phone_number', $doctor->phone_number);
            $stmt->bindParam(':section_id', $doctor->section_id);
            $stmt->execute();
            $doctor->id = $this->connection->lastInsertId();
            return $this->getOne($doctor->id);
        } catch (PDOException $e) {
            echo $e;
        }
    }

    public function update($doctor, $id)
    {
        try {
            $stmt = $this->connection->prepare("UPDATE doctors SET name =:name, email =:email, date_of_birth =:date_of_birth, phone_number =:phone_number, section_id =:section_id WHERE id =:id");
            $stmt->bindParam(':name', $doctor->name);
            $stmt->bindParam(':email', $doctor->email);
            $stmt->bindParam(':date_of_birth', $doctor->date_of_birth);
            $stmt->bindParam(':phone_number', $doctor->phone_number);
            $stmt->bindParam(':section_id', $doctor->section_id);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $this->getOne($id);
        } catch (PDOException $e) {
            echo $e;
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->connection->prepare("DELETE FROM doctors WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e;
        }
    }
}