<?php

namespace Services;

use Repositories\SectionRepository;

class SectionService
{
    private $repository;

    function __construct()
    {
        $this->repository = new SectionRepository();
    }

    public function getAll($sort = NULL, $offset = NULL, $limit = NULL) {
        return $this->repository->getAll($sort, $offset, $limit);
    }

    public function getOne($id) {
        return $this->repository->getOne($id);
    }

//    public function getAllNoOrder() {
//        return $this->repository->getAllNoOrder();
//    }

    public function insert($item) {
        return $this->repository->insert($item);
    }

    public function update($item, $id) {
        return $this->repository->update($item, $id);
    }

    public function delete($id) {
        return $this->repository->delete($id);
    }
}