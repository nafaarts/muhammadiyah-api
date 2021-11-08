<?php

/** @var \Laravel\Lumen\Routing\Router $router */

// $router->get('/', function () use ($router) {
//     return $router->app->version();
// });


$router->group(['middleware' => 'token'], function () use ($router) {
    $router->get('informasi', 'InformasiController@index');
    $router->post('informasi', 'InformasiController@store');
    $router->get('informasi/{slug}', 'InformasiController@show');
    $router->put('informasi/{id}', 'InformasiController@update');
    $router->delete('informasi/{id}', 'InformasiController@destroy');

    $router->get('kategori', 'KategoriController@index');
    $router->post('kategori', 'KategoriController@store');
    $router->get('kategori/{id}', 'KategoriController@show');
    $router->put('kategori/{id}', 'KategoriController@update');
    $router->delete('kategori/{id}', 'KategoriController@destroy');

    $router->get('staff', 'StaffController@index');
    $router->post('staff', 'StaffController@store');
    $router->get('staff/{id}', 'StaffController@show');
    $router->put('staff/{id}', 'StaffController@update');
    $router->delete('staff/{id}', 'StaffController@destroy');

    $router->get('gallery', 'GalleryController@index');
    $router->post('gallery', 'GalleryController@store');
    $router->get('gallery/{id}', 'GalleryController@show');
    $router->put('gallery/{id}', 'GalleryController@update');
    $router->delete('gallery/{id}', 'GalleryController@destroy');

    $router->get('kategori-donasi', 'DonasiKategoriController@index');
    $router->post('kategori-donasi', 'DonasiKategoriController@store');
    $router->get('kategori-donasi/{id}', 'DonasiKategoriController@show');
    $router->put('kategori-donasi/{id}', 'DonasiKategoriController@update');
    $router->delete('kategori-donasi/{id}', 'DonasiKategoriController@destroy');

    $router->get('donasi', 'DonasiController@index');
    $router->post('donasi', 'DonasiController@store');
    $router->get('donasi/{id}', 'DonasiController@show');
    $router->put('donasi/{id}', 'DonasiController@update');
    $router->delete('donasi/{id}', 'DonasiController@destroy');

    $router->get('donatur', 'DonaturController@index');
    $router->post('donatur', 'DonaturController@store');
    $router->get('donatur/{id}', 'DonaturController@show');
    $router->put('donatur/{id}', 'DonaturController@update');
    $router->delete('donatur/{id}', 'DonaturController@destroy');
});

// $router->get('test', function () use ($router) {
//     return response(['msg' => 'Hello'], 200);
// });

// $router->post('test/{id}', function ($id) use ($router) {
//     // if ($request == null) return response(["msg" => "cannot empty!"], 403);
//     return response(["test" => "Hello", "id" => $id], 200);
// });

// $router->get('example', 'ExampleController@index');

// $router->get('hoo', 'TestController@index');
