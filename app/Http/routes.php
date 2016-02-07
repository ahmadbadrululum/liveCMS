<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    $launchingDateTime = globalParams('launching_datetime') ? new Carbon\Carbon(globalParams('launching_datetime')) : Carbon\Carbon::now();

    if ($launchingDateTime->isFuture()) {
        return redirect('coming-soon');
    }

    return redirect('admin');

});

Route::get('coming-soon', function () {
    return view('coming-soon');
});

Route::get('/home', function () {
    return redirect('admin');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    Route::group(['prefix' => 'admin', 'namespace' => 'Backend', 'middleware' => 'auth'], function () {
        Route::get('/', function () {
            return view('admin.home');
        });

        Route::controller('kategori', 'KategoriController');
        Route::controller('tag', 'TagController');
        Route::controller('artikel', 'ArtikelController');
        Route::controller('setting', 'SettingController');
        Route::controller('user', 'UserController');
        Route::controller('kecamatan', 'KecamatanController');
    });

    Route::auth();
});
