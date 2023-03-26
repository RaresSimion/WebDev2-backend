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
//$router->get('/sections?sort=name', 'SectionController@getTest');
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


// routes for the products endpoint
$router->get('/products', 'ProductController@getAll');
$router->get('/products/(\d+)', 'ProductController@getOne');
$router->post('/products', 'ProductController@create');
$router->put('/products/(\d+)', 'ProductController@update');
$router->delete('/products/(\d+)', 'ProductController@delete');


// routes for the categories endpoint
$router->get('/categories', 'CategoryController@getAll');
$router->get('/categories/(\d+)', 'CategoryController@getOne');
$router->post('/categories', 'CategoryController@create');
$router->put('/categories/(\d+)', 'CategoryController@update');
$router->delete('/categories/(\d+)', 'CategoryController@delete');

// routes for the users endpoint
$router->post('/users/login', 'UserController@login');

// Run it!
$router->run();