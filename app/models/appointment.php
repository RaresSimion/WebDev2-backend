<?php

namespace Models;

class Appointment
{
    public int $id;
    public int $user_id;
    public User $user;
    public int $doctor_id;
    public Doctor $doctor;
    public string $date;
    public string $time;
}