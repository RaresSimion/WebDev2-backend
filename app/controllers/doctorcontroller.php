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

        if (isset($_GET["offset"]) && is_numeric($_GET["offset"])) {
            $offset = $_GET["offset"];
        }
        if (isset($_GET["limit"]) && is_numeric($_GET["limit"])) {
            $limit = $_GET["limit"];
        }

        $doctors = $this->service->getAll($offset, $limit);

        $this->respond($doctors);
    }

    public function getOne($id)
    {
        $doctor = $this->service->getOne($id);

        if (!$doctor) {
            $this->respondWithError(404, "Doctor not found");
            return;
        }

        $this->respond($doctor);
    }

    public function create()
    {
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