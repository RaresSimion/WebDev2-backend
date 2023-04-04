<?php

namespace Services;

use Repositories\AppointmentRepository;

class AppointmentService
{
    private $repository;

    function __construct()
    {
        $this->repository = new AppointmentRepository();
    }

    public function getAll($offset = NULL, $limit = NULL) {
        return $this->repository->getAll($offset, $limit);
    }

    public function getAllByUserId($userId, $offset = NULL, $limit = NULL) {
        return $this->repository->getAllByUserId($userId, $offset, $limit);
    }

    public function getOne($id) {
        return $this->repository->getOne($id);
    }

    public function insert($appointment) {
        return $this->repository->insert($appointment);
    }

    public function update($appointment, $id) {
        return $this->repository->update($appointment, $id);
    }

    public function delete($id) {
        return $this->repository->delete($id);
    }

    public function checkDateAndTime($appointment) {
        return $this->repository->checkDateAndTime($appointment);
    }
}