<?php

namespace Controllers;

use Services\DoctorService;
use Exception;

class DoctorController extends Controller
{
    private $service;

    // initialize services
    function __construct()
    {
        $this->service = new DoctorService();
    }

    public function getAll()
    {
        $offset = NULL;
        $limit = NULL;
        $section = NULL;

        if (isset($_GET["offset"]) && is_numeric($_GET["offset"])) {
            $offset = $_GET["offset"];
        }
        if (isset($_GET["limit"]) && is_numeric($_GET["limit"])) {
            $limit = $_GET["limit"];
        }
        if (isset($_GET["section"]) && is_numeric($_GET["section"])) {
            $section = $_GET["section"];
        }

        $doctors = $this->service->getAll($offset, $limit, $section);

        $this->respond($doctors);
    }

    public function getOne($id)
    {
        $token = $this->checkForJwt();
        if (!$token)
            return;
        else if ($token->data->role != "Admin") {
            $this->respondWithError(401, "Unauthorized access, Admin only");
            return;
        }
        $doctor = $this->service->getOne($id);

        if (!$doctor) {
            $this->respondWithError(404, "Doctor not found");
            return;
        }

        $this->respond($doctor);
    }

    public function create()
    {
        $token = $this->checkForJwt();
        if (!$token)
            return;
        else if ($token->data->role != "Admin") {
            $this->respondWithError(401, "Unauthorized access, Admin only");
            return;
        }

        try {

            $doctor = $this->createObjectFromPostedJson("Models\\Doctor");
            $doctor = $this->service->insert($doctor);

        } catch (Exception $e) {
            $this->respondWithError(500, $e->getMessage());
        }

        $this->respond($doctor);
    }

    public function update($id)
    {
        $token = $this->checkForJwt();
        if (!$token)
            return;

        else if ($token->data->role != "Admin") {
            $this->respondWithError(401, "Unauthorized access, Admin only");
            return;
        }
        try {
            $doctor = $this->createObjectFromPostedJson("Models\\Doctor");
            $doctor = $this->service->update($doctor, $id);

        } catch (Exception $e) {
            $this->respondWithError(500, $e->getMessage());
        }

        $this->respond($doctor);
    }

    public function delete($id)
    {
        $token = $this->checkForJwt();

        if (!$token)
            return;

        else if ($token->data->role != "Admin") {
            $this->respondWithError(401, "Unauthorized access, Admin only");
            return;
        }
        try {
            $doctor = $this->service->getOne($id);
            if (!$doctor) {
                $this->respondWithError(404, "Doctor not found");
                return;
            }
            $this->service->delete($id);

        } catch (Exception $e) {
            $this->respondWithError(404, $e->getMessage());
        }

        $this->respond($doctor);
    }
}