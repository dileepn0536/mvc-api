<?php

return [

    'GET' => [
        // ✅ Static routes first (important!)
        'users/create' => 'UserController@createUser',
        'users/test' => 'UserController@test',
        'users/debugging' => 'TestController@testDebugging',
        'cache-check' => 'UserController@cacheCheck',

        // Dynamic routes later
        'users' => 'UserController@index',
        'users/show/{id}' => 'UserController@showUser',
    ],

    'POST' => [
        'users/store' => 'UserController@storeUser',
    ],

    'PUT' => [
        'users/{id}' => 'UserController@updateUser',
    ],

    'DELETE' => [
        'users/{id}' => 'UserController@deleteUser',
    ],

];