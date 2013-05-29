<?php namespace Regulus\OpenForum;

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;

use Regulus\SolidSite\SolidSite as Site;

Route::filter('forum', function()
{
	//set site section
	Site::set('section', 'Forum');

	//add initial breadcrumb trail item for forum
	Site::addTrailItem('Forum', 'forum');

	//prepare views
	View::composer('open-forum::home', function($view)
	{
		$sections = ForumSection::all();
		$view->with('sections', $sections);
	});
});

Route::filter('ajax', function()
{
	if (!Request::ajax()) exit;
});

/* Set Filters */
Route::when('forum',       'forum');
Route::when('forum/*',      'forum');
Route::when('forum/ajax/*', 'ajax');