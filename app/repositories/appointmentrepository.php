<?php

namespace Repositories;

use Models\Appointment;
use Models\User;
use Models\Doctor;
use PDO;
use PDOException;
use Repositories\Repository;

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
            $query = "SELECT appointments.*, users.id as user_id, doctors.id as doctor_id FROM appointments INNER JOIN users ON appointments.user_id=users.id INNER JOIN doctors ON appointments.doctor_id=doctors.id";
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
            $query = "SELECT appointments.*, users.id as user_id, doctors.id as doctor_id FROM appointments INNER JOIN users ON appointments.user_id=users.id INNER JOIN doctors ON appointments.doctor_id=doctors.id WHERE appointments.user_id=:user_id";
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
            $query = "SELECT appointments.*, users.id as user_id, doctors.id as doctor_id FROM appointments INNER JOIN users ON appointments.user_id=users.id INNER JOIN doctors ON appointments.doctor_id=doctors.id WHERE appointments.id=:id";
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

    public function checkAppointment($appointment)
    {
        try {
            $closest_time = $this->getClosestAppointmentTime($appointment);
            if($closest_time)
            {
                $time_diff = strtotime($appointment->time) - strtotime($closest_time);
                if($time_diff >= 3600)
                {
                    return true;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return true;
            }
//            $query = "SELECT * FROM appointments WHERE doctor_id=:doctor_id AND date=:date AND time>=:time + INTERVAL 30 MINUTE";
//            $stmt = $this->connection->prepare($query);
//            $stmt->bindParam(':doctor_id', $appointment->doctor_id);
//            $stmt->bindParam(':date', $appointment->date);
//            $stmt->bindParam(':time', $appointment->time);
//            $stmt->execute();
//            $row = $stmt->fetch(PDO::FETCH_ASSOC);
//            return $this->rowToAppointment($row);
        } catch (PDOException $e) {
            echo $e;
        }
    }

    public function getClosestAppointmentTime($appointment)
    {
        try {
            $query = "SELECT time FROM appointments
          WHERE doctor_id = :doctor_id AND date = :date
          ORDER BY ABS(TIMESTAMPDIFF(SECOND, time, :time)) ASC
          LIMIT 1";
            $stmt = $this->connection->prepare($query);
            $stmt->bindParam(':doctor_id', $appointment->doctor_id);
            $stmt->bindParam(':date', $appointment->date);
            $stmt->bindParam(':time', $appointment->time);
            $stmt->execute();
            $closest_time = $stmt->fetchColumn();
            return $closest_time;
        } catch (PDOException $e) {
            echo $e;
        }
    }

}