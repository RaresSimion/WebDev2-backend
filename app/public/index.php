<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

error_reporting(E_ALL);
ini_set("display_errors", 1);

require __DIR__ . '/../vendor/autoload.php';

// Create Router instance
$router = new \Bramus\Router\Router();

$router->setNamespace('Controllers');

// routes for the sections endpoint
$router->get('/sections', 'SectionController@getAll');
$router->get('/sections/(\d+)', 'SectionController@getOne');
$router->post('/sections', 'SectionController@create');
$router->put('/sections/(\d+)', 'SectionController@update');
$router->delete('/sections/(\d+)', 'SectionController@delete');

//routes for the user types endpoint
$router->get('/usertypes', 'UserTypeController@getAll');
$router->get('/usertypes/(\d+)', 'UserTypeController@getOne');
$router->post('/usertypes', 'UserTypeController@create');
$router->put('/usertypes/(\d+)', 'UserTypeController@update');
$router->delete('/usertypes/(\d+)', 'UserTypeController@delete');

// routes for the doctors endpoint
$router->get('/doctors', 'DoctorController@getAll');
$router->get('/doctors/(\d+)', 'DoctorController@getOne');
$router->post('/doctors', 'DoctorController@create');
$router->put('/doctors/(\d+)', 'DoctorController@update');
$router->delete('/doctors/(\d+)', 'DoctorController@delete');

// routes for the users endpoint
$router->get('/users', 'UserController@getAll');
$router->get('/users/(\d+)', 'UserController@getOne');
$router->post('/users/register', 'UserController@register');
$router->put('/users/(\d+)', 'UserController@update');
$router->delete('/users/(\d+)', 'UserController@delete');
$router->post('/users/login', 'UserController@login');
$router->put('/users/promote/(\d+)', 'UserController@promote');

// routes for the appointments endpoint
$router->get('/appointments', 'AppointmentController@getAll');
$router->get('/appointments/user/(\d+)', 'AppointmentController@getUserAppointments');
$router->get('/appointments/(\d+)', 'AppointmentController@getOne');
$router->post('/appointments', 'AppointmentController@create');
$router->put('/appointments/(\d+)', 'AppointmentController@update');
$router->delete('/appointments/(\d+)', 'AppointmentController@delete');

$router->run();