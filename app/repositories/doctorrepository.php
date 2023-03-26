<?php

namespace Repositories;

use Models\Doctor;
use Models\Section;
use PDO;
use PDOException;
use Repositories\Repository;

class DoctorRepository extends Repository
{
    function rowToDoctor($row)
    {
        $doctor = new Doctor();
        $doctor->id = $row['id'];
        $doctor->name = $row['name'];
        $doctor->email = $row['email'];
        $doctor->date_of_birth = $row['date_of_birth'];
        $doctor->phone_number = $row['phone_number'];
        $section = new Section();
        $section->id = $row['section'];
        $section->name = $row['section_name'];
        $doctor->section = $section;

        return $doctor;
    }
    public function getAll($offset = NULL, $limit = NULL)
    {
        try {
            $query = "SELECT doctors.*, clinic_sections.name as section_name FROM doctors INNER JOIN clinic_sections ON doctors.section=clinic_sections.id;
";
            if (isset($limit) && isset($offset)) {
                $query .= " LIMIT :limit OFFSET :offset ";
            }

            $stmt = $this->connection->prepare($query);
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
}