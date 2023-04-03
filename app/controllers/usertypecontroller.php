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
        $jwt = $this->checkForJwt();
        if (!$jwt)
            return;
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

        $userTypes = $this->service->getAll($offset, $limit);

        $this->respond($userTypes);
    }

    public function getOne($id)
    {
        $jwt = $this->checkForJwt();
        if (!$jwt)
            return;
        else if ($jwt->data->role != "Admin") {
            $this->respondWithError(401, "Unauthorized access, Admin only");
            return;
        }
        $userType = $this->service->getOne($id);

        if (!$userType) {
            $this->respondWithError(404, "UserType not found");
            return;
        }

        $this->respond($userType);
    }

    public function create()
    {
        $jwt = $this->checkForJwt();
        if (!$jwt)
            return;
        else if ($jwt->data->role != "Admin") {
            $this->respondWithError(401, "Unauthorized access, Admin only");
            return;
        }
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
        $jwt = $this->checkForJwt();
        if (!$jwt)
            return;
        else if ($jwt->data->role != "Admin") {
            $this->respondWithError(401, "Unauthorized access, Admin only");
            return;
        }
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
        $jwt = $this->checkForJwt();
        if (!$jwt)
            return;
        else if ($jwt->data->role != "Admin") {
            $this->respondWithError(401, "Unauthorized access, Admin only");
            return;
        }
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