<?php

namespace Controllers;

use Exception;
use Services\SectionService;

class SectionController extends Controller
{
    private $service;

    // initialize services
    function __construct()
    {
        $this->service = new SectionService();
    }

    public function getAll()
    {

        $offset = NULL;
        $limit = NULL;
        $sort = NULL;

        if (isset($_GET["sort"])) {
            $sort = $_GET["sort"];
        }

        if (isset($_GET["offset"]) && is_numeric($_GET["offset"])) {
            $offset = $_GET["offset"];
        }
        if (isset($_GET["limit"]) && is_numeric($_GET["limit"])) {
            $limit = $_GET["limit"];
        }

        $sections = $this->service->getAll($sort, $offset, $limit);

        $this->respond($sections);
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
        $section = $this->service->getOne($id);

        if (!$section) {
            $this->respondWithError(404, "Section not found");
            return;
        }

        $this->respond($section);
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
            $section = $this->createObjectFromPostedJson("Models\\Section");
            $this->service->insert($section);
        } catch (Exception $e) {
            $this->respondWithError(500, $e->getMessage());
        }

        $this->respond($section);
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
            $section = $this->service->getOne($id);

            if (!$section) {
                $this->respondWithError(404, "Section not found");
                return;
            }
            $section = $this->createObjectFromPostedJson("Models\\Section");
            $this->service->update($section, $id);
        } catch (Exception $e) {
            $this->respondWithError(500, $e->getMessage());
        }

        $this->respond($section);
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
            $section = $this->service->getOne($id);

            if (!$section) {
                $this->respondWithError(404, "Section not found");
                return;
            }
            $this->service->delete($id);
        } catch (Exception $e) {
            $this->respondWithError(500, $e->getMessage());
        }

        $this->respond($section);
    }

}