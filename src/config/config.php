<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Layout
	|--------------------------------------------------------------------------
	|
	| The location of your forum view layout. It is defaulted to
	| "open-forum::layouts.master" to use OpenForum's built-in view layout,
	| but you may point it towards a directory of your own for full layout
	| customization.
	|
	*/
	'layout'                => 'layouts.master',

	/*
	|--------------------------------------------------------------------------
	| Layout Section
	|--------------------------------------------------------------------------
	|
	| The name of the layout section the forum should appear in.
	|
	*/
	'section'               => 'content',

	/*
	|--------------------------------------------------------------------------
	| Views Location
	|--------------------------------------------------------------------------
	|
	| The location of your forum views. It is defaulted to "open-forum::" to
	| use OpenForum's built-in views, but you may point it towards a views
	| directory of your own for full view customization.
	|
	*/
	'viewsLocation'         => 'open-forum::',

	/*
	|--------------------------------------------------------------------------
	| Authorization Class
	|--------------------------------------------------------------------------
	|
	| The name of your authorization class including the namespace and a
	| leading backslash. This variable along with the "authMethod" variables
	| allow OpenForum's built-in views to remain authoriztion class agnostic.
	| The default is "\Illuminate\Support\Facades\Auth" which is Laravel 4's
	| native authorization class.
	|
	*/
	'authClass'             => '\Illuminate\Support\Facades\Auth',

	/*
	|--------------------------------------------------------------------------
	| Authorization Method - Authentication Check
	|--------------------------------------------------------------------------
	|
	| The method in your authorization class that checks if user is logged in.
	| The default is "check()" which, along with the default auth class above,
	| selects Laravel 4's native authentication method.
	|
	*/
	'authMethodActiveCheck' => 'check()',

	/*
	|--------------------------------------------------------------------------
	| Authorization Method - Admin Check
	|--------------------------------------------------------------------------
	|
	| The method in your authorization class that checks if the logged in user
	| is an administrator. Set this to false if you do not have a method of
	| identifying an admin.
	|
	*/
	'authMethodAdminCheck'  => false,
);