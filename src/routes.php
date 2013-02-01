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
use Illuminate\Support\Facades\View;

//add short routes for all forum sections ("forum/general" instead of "forum/section/general")
$sections = ForumSection::get(array('uri_tag'));
foreach ($sections as $section) {
	Route::get('forum/'.$section->uri_tag, 'Regulus\OpenForum\ForumController@getSection');
}

//map entire controller
Route::controller('forum', 'Regulus\OpenForum\ForumController');