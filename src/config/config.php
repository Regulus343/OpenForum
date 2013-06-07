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
	| Authorization Method - User
	|--------------------------------------------------------------------------
	|
	| The method for getting the active user.
	|
	*/
	'authMethodActiveUser' => 'user()',

	/*
	|--------------------------------------------------------------------------
	| Authorization Method - User ID
	|--------------------------------------------------------------------------
	|
	| The attribute for getting the active user ID which is used in conjunction
	| with the user method above. By default, they get "user()->id" together.
	|
	*/
	'authMethodActiveUserID' => 'id',

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

	/*
	|--------------------------------------------------------------------------
	| Authorization - Admin Role
	|--------------------------------------------------------------------------
	|
	| The name of the admin role if admin check is enabled.
	|
	*/
	'authMethodAdminRole' => 'admin',

	/*
	|--------------------------------------------------------------------------
	| Post Edit Limit (in seconds)
	|--------------------------------------------------------------------------
	|
	| The post editing limit in seconds. By default, users may edit or
	| delete their post for 180 seconds after initial post.
	|
	*/
	'postEditLimit' => 180,

	/*
	|--------------------------------------------------------------------------
	| Post Minimum Length
	|--------------------------------------------------------------------------
	|
	| The minimum length of characters for a post. Set to false if for no
	| minimum. The default is 16 characters to prevent pointless "First!"
	| posts and other short, useless posts.
	|
	*/
	'postMinLength' => 16,

	/*
	|--------------------------------------------------------------------------
	| Post Wait Time (in seconds)
	|--------------------------------------------------------------------------
	|
	| The minimum length of time in seconds that must pass between posts
	| for a particular user. The default is 90 seconds. This can prevent a
	| user from flooding your website.
	|
	*/
	'postWaitTime' => 90,

	/*
	|--------------------------------------------------------------------------
	| Post Order
	|--------------------------------------------------------------------------
	|
	| The order that the posts appear in, "asc" being oldest to newest and
	| "desc" being newest to oldest.
	|
	*/
	'postOrder' => 'desc',

	/*
	|--------------------------------------------------------------------------
	| Posts Per Page
	|--------------------------------------------------------------------------
	|
	| The number of posts per page. Pagination buttons exist in the posts
	| area to allow the user to page through all posts.
	|
	*/
	'postsPerPage' => 20,

	/*
	|--------------------------------------------------------------------------
	| Threads Per Page
	|--------------------------------------------------------------------------
	|
	| The number of threads per page. Pagination buttons exist in the threads
	| area to allow the user to page through all threads.
	|
	*/
	'threadsPerPage' => 30,

	/*
	|--------------------------------------------------------------------------
	| Post Auto-Approval
	|--------------------------------------------------------------------------
	|
	| Determines whether the posts should be auto-approved and show up
	| immediately or whether they are subject to approval by the administrator
	| first. Auto-approval is turned on by default.
	|
	*/
	'postAutoApproval' => true,

	/*
	|--------------------------------------------------------------------------
	| Load jQuery
	|--------------------------------------------------------------------------
	|
	| Whether or not to have Open Comments automatically load jQuery.
	| Turn this off if your website already loads jQuery.
	|
	*/
	'loadJquery' => true,

	/*
	|--------------------------------------------------------------------------
	| Load Bootstrap
	|--------------------------------------------------------------------------
	|
	| Whether or not to have Open Comments automatically load Twitter Bootsrap.
	| If set to false, Open Comments will assume you are already loading
	| Bootstrap CSS and JS files. If true, Open Comments will attempt to load
	| "bootstrap.css" and "bootstrap.min.js".
	|
	*/
	'loadBootstrap' => true,

	/*
	|--------------------------------------------------------------------------
	| Load Boxy
	|--------------------------------------------------------------------------
	|
	| By default, Open Comments makes use of the lightweight javascript
	| library Boxy for modal windows like comment deleting confirmation.
	| You may turn off Boxy if you intend to use another modal window script.
	|
	*/
	'loadBoxy' => true,

);