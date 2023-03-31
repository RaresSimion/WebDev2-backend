<?php

namespace Controllers;

use Exception;
use Services\UserService;
use \Firebase\JWT\JWT;

class UserController extends Controller
{
    private $service;

    // initialize services
    function __construct()
    {
        $this->service = new UserService();
    }

    public function getAll() {
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
        $user = $this->service->getOne($id);
        if(!$user) {
            $this->respondWithError(404, "User not found");
            return;
        }

        $this->service->delete($id);
        $this->respond($user);
    }

    public function login() {

        // read user data from request body
        $postedUser = $this->createObjectFromPostedJson("Models\\User");

        // get user from db
        $user = $this->service->checkEmailPassword($postedUser->email, $postedUser->password);

        // if the method returned false, the username and/or password were incorrect
        if(!$user) {
            $this->respondWithError(401, "Invalid login");
            return;
        }

        // generate jwt
        $tokenResponse = $this->generateJwt($user);       

        $this->respond($tokenResponse);    
    }

    public function generateJwt($user) {
        $secret_key = "YOUR_SECRET_KEY";

        $issuer = "THE_ISSUER"; // this can be the domain/servername that issues the token
        $audience = "THE_AUDIENCE"; // this can be the domain/servername that checks the token

        $issuedAt = time(); // issued at
        $notbefore = $issuedAt; //not valid before 
        $expire = $issuedAt + 600; // expiration time is set at +600 seconds (10 minutes)

        // JWT expiration times should be kept short (10-30 minutes)
        // A refresh token system should be implemented if we want clients to stay logged in for longer periods

        // note how these claims are 3 characters long to keep the JWT as small as possible
        $payload = array(
            "iss" => $issuer,
            "aud" => $audience,
            "iat" => $issuedAt,
            "nbf" => $notbefore,
            "exp" => $expire,
            "data" => array(
                "id" => $user->id,
                "email" => $user->email,
        ));

        $jwt = JWT::encode($payload, $secret_key, 'HS256');

        return 
            array(
                "message" => "Successful login.",
                "jwt" => $jwt,
                "username" => $user->email,
                "expireAt" => $expire
            );
    }    
}
