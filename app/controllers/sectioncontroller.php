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
        // Checks for a valid jwt, returns 401 if none is found
//        $token = $this->checkForJwt();
//        if (!$token)
//            return;

        $offset = NULL;
        $limit = NULL;
        $sort = NULL;

        //$sort = $_GET['sort'] ?? 'id';
//        if(!isset($sort))
//        {
//            $sort = 'id';
//        }

        if (isset($_GET["bruh"]) && !is_string($_GET["bruh"])) {
            $sort = $_GET["bruh"];
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
        $section = $this->service->getOne($id);

        if (!$section) {
            $this->respondWithError(404, "Section not found");
            return;
        }

        $this->respond($section);
    }

    public function create()
    {
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