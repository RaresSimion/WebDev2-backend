<?php
namespace Services;

use Repositories\UserRepository;

class UserService {

    private $repository;

    function __construct()
    {
        $this->repository = new UserRepository();
    }

    public function getAll($offset = NULL, $limit = NULL) {
        return $this->repository->getAll($offset, $limit);
    }

    public function getOne($id) {
        return $this->repository->getOne($id);
    }

    public function insert($user) {
        return $this->repository->insert($user);
    }

    public function update($user, $id) {
        return $this->repository->update($user, $id);
    }

    public function delete($id) {
        $this->repository->delete($id);
    }

    public function promoteToAdmin($id) {
        return $this->repository->promoteToAdmin($id);
    }

    public function checkEmailPassword($email, $password) {
        return $this->repository->checkEmailPassword($email, $password);
    }

    public function checkEmail($email) {
        return $this->repository->checkEmail($email);
    }
}

?>