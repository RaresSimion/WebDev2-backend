<?php

namespace Controllers;

use Services\AppointmentService;
use Exception;
class AppointmentController extends Controller
{
    private $service;

    function __construct()
    {
        $this->service = new AppointmentService();
    }

    public function getAll()
    {
        $jwt = $this->checkForJwt();
        if (!$jwt) {
            return;
        }
        else if ($jwt->data->role != "Admin") {
            $this->respondWithError(401, "Unauthorized access, Admin only");
            return;
        }
        $offset = NULL;
        $limit = NULL;

        if (isset($_GET["offset"]) && is_numeric($_GET["offset"])) {
            $offset = $_GET["offset"];
        }
        if (isset($_GET["limit"]) && is_numeric($_GET["limit"])) {
            $limit = $_GET["limit"];
        }

        $appointments = $this->service->getAll($offset, $limit);

        $this->respond($appointments);
    }

    public function getUserAppointments($id)
    {
        //any user can get their own appointments
        $jwt = $this->checkForJwt();
        if (!$jwt) {
            return;
        }
        $offset = NULL;
        $limit = NULL;

        if (isset($_GET["offset"]) && is_numeric($_GET["offset"])) {
            $offset = $_GET["offset"];
        }
        if (isset($_GET["limit"]) && is_numeric($_GET["limit"])) {
            $limit = $_GET["limit"];
        }

        $appointments = $this->service->getAllByUserId($id, $offset, $limit);
        if(!$appointments){
            $this->respondWithError(404, "No appointments booked yet.");
            return;
        }

        $this->respond($appointments);
    }

    public function getOne($id)
    {
        $jwt = $this->checkForJwt();
        if (!$jwt) {
            return;
        }
        $appointment = $this->service->getOne($id);

        if (!$appointment) {
            $this->respondWithError(404, "Appointment not found");
            return;
        }

        $this->respond($appointment);
    }

    public function create()
    {
        $jwt = $this->checkForJwt();
        if (!$jwt) {
            return;
        }
        try {
            $appointment = $this->createObjectFromPostedJson("Models\\Appointment");

            //check if the appointment time is already taken
            if(!$this->service->checkDateAndTime($appointment)){
                $this->respondWithError(400, "The appointment is already taken, select a different time.");
                return;
            }

            $appointment = $this->service->insert($appointment);

        } catch (Exception $e) {
            $this->respondWithError(500, $e->getMessage());
        }

        $this->respond($appointment);
    }

    public function update($id)
    {
        $jwt = $this->checkForJwt();
        if (!$jwt) {
            return;
        }
        try {
            $appointment = $this->service->getOne($id);

            if (!$appointment) {
                $this->respondWithError(404, "Appointment not found");
                return;
            }

            $postedAppointment = $this->createObjectFromPostedJson("Models\\Appointment");

            //check if the appointment time is already taken
            if(!$this->service->checkDateAndTime($postedAppointment)){
                $this->respondWithError(400, "The appointment is already taken, select a different time.");
                return;
            }

            $appointment = $this->service->update($postedAppointment, $id);

        } catch (Exception $e) {
            $this->respondWithError(500, $e->getMessage());
        }

        $this->respond($appointment);
    }

    public function delete($id)
    {
        $jwt = $this->checkForJwt();
        if (!$jwt) {
            return;
        }
        $appointment = $this->service->getOne($id);

        if (!$appointment) {
            $this->respondWithError(404, "Appointment not found");
            return;
        }

        $this->service->delete($id);

        $this->respond($appointment);
    }
}
{

}