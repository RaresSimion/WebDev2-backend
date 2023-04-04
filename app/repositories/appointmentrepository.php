<?php

namespace Repositories;

use Models\Appointment;
use PDO;
use PDOException;

class AppointmentRepository extends Repository
{
    public function rowToAppointment($row)
    {
        if($row == null) {
            return null;
        }
        $appointment = new Appointment();
        $appointment->id = $row['id'];
        $appointment->user_id = $row['user_id'];
        $appointment->doctor_id = $row['doctor_id'];
        $appointment->date = $row['date'];
        $appointment->time = $row['time'];

        //not sure if this is the best way to do this
        $userRep = new UserRepository();
        $user = $userRep->getOne($row['user_id']);
        $appointment->user = $user;

        $doctorRep = new DoctorRepository();
        $doctor = $doctorRep->getOne($row['doctor_id']);
        $appointment->doctor = $doctor;

        return $appointment;
    }

    public function getAll($offset = NULL, $limit = NULL)
    {
        try {
            //could have used multiple joins to get the user and doctor info
            //I thought I should make use of the UserRep and DoctorRep and just get the user_id and doctor_id
            $query = "SELECT * FROM appointments";
            if (isset($limit) && isset($offset)) {
                $query .= " LIMIT :limit OFFSET :offset ";
            }
            $stmt = $this->connection->prepare($query);
            if (isset($limit) && isset($offset)) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            }

            $stmt->execute();
            $appointments = array();
            while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
                $appointments[] = $this->rowToAppointment($row);
            }

            return $appointments;
        } catch (PDOException $e) {
            echo $e;
        }
    }

    public function getAllByUserId($user_id, $offset = NULL, $limit = NULL)
    {
        try {
            $query = "SELECT * FROM appointments WHERE appointments.user_id=:user_id";
            if (isset($limit) && isset($offset)) {
                $query .= " LIMIT :limit OFFSET :offset ";
            }
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            if (isset($limit) && isset($offset)) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            }

            $stmt->execute();
            $appointments = array();
            while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
                $appointments[] = $this->rowToAppointment($row);
            }

            return $appointments;
        } catch (PDOException $e) {
            echo $e;
        }
    }

    public function getOne($id)
    {
        try {
            $query = "SELECT * FROM appointments WHERE appointments.id=:id";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $this->rowToAppointment($row);
        } catch (PDOException $e) {
            echo $e;
        }
    }

    public function insert($appointment)
    {
        try {
            $query = "INSERT INTO appointments (user_id, doctor_id, date, time) VALUES (:user_id, :doctor_id, :date, :time)";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':user_id', $appointment->user_id);
            $stmt->bindParam(':doctor_id', $appointment->doctor_id);
            $stmt->bindParam(':date', $appointment->date);
            $stmt->bindParam(':time', $appointment->time);
            $stmt->execute();
            $appointment->id = $this->connection->lastInsertId();
            return $appointment;
        } catch (PDOException $e) {
            echo $e;
        }
    }

    public function update($appointment, $id)
    {
        try {
            $query = "UPDATE appointments SET date=:date, time=:time WHERE id=:id";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':date', $appointment->date);
            $stmt->bindParam(':time', $appointment->time);
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
            $query = "DELETE FROM appointments WHERE id=:id";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            echo $e;
        }
    }

    public function checkDateAndTime($appointment)
    {
        try {
            $query = "SELECT * FROM appointments WHERE doctor_id=:doctor_id AND date=:date AND time=:time";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':doctor_id', $appointment->doctor_id);
            $stmt->bindParam(':date', $appointment->date);
            $stmt->bindParam(':time', $appointment->time);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $appointment = $this->rowToAppointment($row);
            if($appointment)
            {
                return false;
            }
            else
            {
                return true;
            }
        } catch (PDOException $e) {
            echo $e;
        }
    }

}