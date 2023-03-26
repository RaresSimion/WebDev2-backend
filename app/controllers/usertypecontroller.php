<?php

namespace Controllers;

use Exception;
use Services\UserTypeService;

class UserTypeController extends Controller
{
    private $service;

    // initialize services
    function __construct()
    {
        $this->service = new UserTypeService();
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

        $userTypes = $this->service->getAll($offset, $limit);

        $this->respond($userTypes);
    }

    public function getOne($id)
    {
        $userType = $this->service->getOne($id);

        if (!$userType) {
            $this->respondWithError(404, "UserType not found");
            return;
        }

        $this->respond($userType);
    }

    public function create()
    {
        try {
            $userType = $this->createObjectFromPostedJson("Models\\UserType");
            $userType = $this->service->insert($userType);

        } catch (Exception $e) {
            $this->respondWithError(500, $e->getMessage());
        }

        $this->respond($userType);
    }

    public function update($id)
    {
        try {
            $userType = $this->service->getOne($id);

            if (!$userType) {
                $this->respondWithError(404, "UserType not found");
                return;
            }
            $userType = $this->createObjectFromPostedJson("Models\\UserType");
            $userType = $this->service->update($userType, $id);

        } catch (Exception $e) {
            $this->respondWithError(500, $e->getMessage());
        }

        $this->respond($userType);
    }

    public function delete($id)
    {
        try {
            $userType = $this->service->getOne($id);

            if (!$userType) {
                $this->respondWithError(404, "Section not found");
                return;
            }
            $this->service->delete($id);

        } catch (Exception $e) {
            $this->respondWithError(500, $e->getMessage());
        }

        $this->respond($userType);
    }

}