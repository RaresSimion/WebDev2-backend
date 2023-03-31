<?php
namespace Models;

class User {

    public int $id;
    public string $first_name;
    public string $last_name;
    public string $address;
    public string $phone_number;
    public string $date_of_birth;
    public string $gender;
    public string $email;
    public string $password;
    public int $user_type_id;
    public UserType $user_type;

}

?>