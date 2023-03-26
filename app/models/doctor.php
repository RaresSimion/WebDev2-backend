<?php

namespace Models;

class Doctor
{
    public int $id;
    public string $name;
    public Section $section;
    public string $email;
    public string $date_of_birth;
    public string $phone_number;
}