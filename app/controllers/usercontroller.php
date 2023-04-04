<?php

namespace Controllers;

use Exception;
use Services\UserService;
use \Firebase\JWT\JWT;

class UserController extends Controller
{
    private $service;

    function __construct()
    {
        $this->service = new UserService();
    }

    public function getAll() {
        $jwt = $this->checkForJwt();
        if (!$jwt) {
            return;
        }
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

        $users = $this->service->getAll($offset, $limit);

        $this->respond($users);
    }

    public function getOne($id) {
        $jwt = $this->checkForJwt();
        if(!$jwt)
            return;

        $user = $this->service->getOne($id);

        if(!$user) {
            $this->respondWithError(404, "User not found");
            return;
        }

        $this->respond($user);
    }

    public function register() {
        try {
            $postedUser = $this->createObjectFromPostedJson("Models\\User");

            // check if email is already in use
            if($this->service->checkEmail($postedUser->email)) {
                $this->respondWithError(400, "Email already in use");
                return;
            }
            else
            {
                $user = $this->service->insert($postedUser);
            }

        } catch (Exception $e) {
            $this->respondWithError(500, $e->getMessage());
        }

        $this->respond($user);
    }

    public function update($id) {
        $jwt = $this->checkForJwt();
        if(!$jwt)
            return;
        try {
            $user = $this->service->getOne($id);
            if(!$user) {
                $this->respondWithError(404, "User not found");
                return;
            }

            $postedUser = $this->createObjectFromPostedJson("Models\\User");
            $user = $this->service->update($postedUser, $id);

        } catch (Exception $e) {
            $this->respondWithError(500, $e->getMessage());
        }

        $this->respond($user);
    }

    public function delete($id) {
        $jwt = $this->checkForJwt();
        if(!$jwt)
            return;
        $user = $this->service->getOne($id);
        if(!$user) {
            $this->respondWithError(404, "User not found");
            return;
        }

        $this->service->delete($id);
        $this->respond($user);
    }

    public function promote($id) {
        $jwt = $this->checkForJwt();
        if(!$jwt)
            return;
        else if ($jwt->data->role != "Admin") {
            $this->respondWithError(401, "Unauthorized access, Admin only");
            return;
        }
        $user = $this->service->getOne($id);
        if(!$user) {
            $this->respondWithError(404, "User not found");
            return;
        }

        $user = $this->service->promoteToAdmin($id);
        $this->respond($user);
    }

    public function login() {

        $postedUser = $this->createObjectFromPostedJson("Models\\User");

        //check if posted user exists in the database
        $user = $this->service->checkEmailPassword($postedUser->email, $postedUser->password);

        if(!$user) {
            $this->respondWithError(401, "Invalid login");
            return;
        }

        //generate jwt
        $tokenResponse = $this->generateJwt($user);       

        $this->respond($tokenResponse);    
    }

    public function generateJwt($user) {
        $secret_key = "webdev2-rares";

        $issuer = "Miracle Clinic";
        $audience = "THE_AUDIENCE";

        $issuedAt = time();
        $notbefore = $issuedAt;
        $expire = $issuedAt + 1200; // expiration time is set at +1200 seconds (20 minutes)

        $payload = array(
            "iss" => $issuer,
            "aud" => $audience,
            "iat" => $issuedAt,
            "nbf" => $notbefore,
            "exp" => $expire,
            "data" => array(
                "user_id" => $user->id,
                "email" => $user->email,
                "role" => $user->user_type->name
        ));

        $jwt = JWT::encode($payload, $secret_key, 'HS256');

        return 
            array(
                "message" => "Successful login.",
                "jwt" => $jwt,
                "first_name" => $user->first_name,
                "user_id" => $user->id,
                "role" => $user->user_type->name,
                "expireAt" => $expire
            );
    }    
}
