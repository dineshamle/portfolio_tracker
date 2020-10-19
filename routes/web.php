<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', 'SearchstockController@index')->name('landingpage');
// Route::get('searchstock', 'SearchstockController@searchStock');
Route::post('/searchstock', 'SearchstockController@searchStock')->name('searchstock.post');

Route::get('stock/{id}', 'StockController@index')->name('stock.index');

Route::get('/validate-github', 'ValidategithubController@validateGithub')->name('validate-github');

Route::get('/logout', 'UserController@logout')->name('logout');

Route::post('/portfolio/add', 'PortfolioController@add')->name('portfolio-add');
Route::get('/portfolio/view', 'PortfolioController@view')->name('portfolio-view');

Route::post('/portfolio/update', 'PortfolioController@update')->name('portfolio-update');
Route::post('/portfolio/delete', 'PortfolioController@delete')->name('portfolio-delete');