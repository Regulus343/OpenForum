<?php namespace Regulus\OpenForum;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

use Illuminate\Support\Facades\Route;

/* Add Short Routes for Forum Sections ("forum/general" in addition to "forum/section/general") */
$sections = ForumSection::get(array('slug'));
foreach ($sections as $section) {
	Route::any('forum/{'.$section->slug.'}', 'Regulus\OpenForum\ForumController@getSection');
}

/* Create, Edit, and Delete - Thread & Post */
Route::get('forum/thread/create/{slug?}', 'Regulus\OpenForum\ForumController@getCreateThread');
Route::post('forum/thread/create/{slug?}', 'Regulus\OpenForum\ForumController@postCreateThread');

/* Map Controller */
Route::controller('forum',                'Regulus\OpenForum\ForumController');