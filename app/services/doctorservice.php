<?php

namespace Services;

use Repositories\DoctorRepository;

class DoctorService
{
    private $repository;

    function __construct()
    {
        $this->repository = new DoctorRepository();
    }

    public function getAll($offset = NULL, $limit = NULL) {
        return $this->repository->getAll($offset, $limit);
    }

    public function getOne($id) {
        return $this->repository->getOne($id);
    }

    public function insert($doctor) {
        return $this->repository->insert($doctor);
    }

    public function update($doctor, $id) {
        return $this->repository->update($doctor, $id);
    }

    public function delete($id) {
        return $this->repository->delete($id);
    }
}