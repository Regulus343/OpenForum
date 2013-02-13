<?php namespace Regulus\OpenForum;

/*----------------------------------------------------------------------------------------------------------
	OpenForum
		A light, effective discussion forum composer package that is easy to configure and implement.

		created by Cody Jassman
		last updated on February 5, 2013
----------------------------------------------------------------------------------------------------------*/

use Illuminate\Support\Facades\Config;

class OpenForum {

	public static $auth;

	/**
	 * Get the list of available forum sections.
	 *
	 * @return array
	 */
	public static function getSections()
	{
		return ForumSection::all();
	}

	/**
	 * Get a section by its ID or URI tag.
	 *
	 * @param  mixed    $id
	 * @return array
	 */
	public static function getSection($id)
	{
		return ForumSection::where('id', '=', $id)->orWhere('uri_tag', '=', $id)->first();
	}

	/**
	 * Authenticates users for the default OpenForum views while remaining authorization class-agnostic.
	 *
	 * @return boolean
	 */
	public static function auth()
	{
		$auth = static::configAuth();
		if ($auth->methodActiveCheck != false) {
			$function = static::separateFunction($auth->methodActiveCheck);
			return static::callFunction($function);
		}
		return false;
	}

	/**
	 * Authenticates admin for the default OpenForum views while remaining authorization class-agnostic.
	 *
	 * @return boolean
	 */
	public static function admin()
	{
		$auth = static::configAuth();
		if ($auth->methodAdminCheck) {
			$function = static::separateFunction($auth->methodAdminCheck);
			return static::callFunction($function);
		}
		return false;
	}

	/**
	 * Prepare authorization configuration.
	 *
	 * @return array
	 */
	private static function configAuth()
	{
		if (is_null(static::$auth)) {
			static::$auth = (object) array(
				'class'             => Config::get('open-forum::authClass'),
				'methodActiveCheck' => Config::get('open-forum::authMethodActiveCheck'),
				'methodAdminCheck'  => Config::get('open-forum::authMethodAdminCheck'),
			);
		}
		return static::$auth;
	}

	/**
	 * Separates a function string "function('array')" into the
	 * function name and the parameters for use with call_user_func.
	 *
	 * @param  string   $function
	 * @return object
	 */
	public static function separateFunction($function)
	{
		$data = preg_match('/([\w\_\d]+)\(([\w\W]*)\)/', $function, $matches);
		if (!isset($matches[0])) $matches[0] = $function;
		if (!isset($matches[1])) $matches[1] = str_replace('()', '', $function);
		if (!isset($matches[2])) $matches[2] = null;
		return (object) array(
			'method'     => $matches[1],
			'parameters' => str_replace("'", '', $matches[2]),
		);
	}

	/**
	 * Calls a function using call_user_func and call_user_func array.
	 *
	 * @param  object   $function
	 * @return boolean
	 */
	public static function callFunction($function)
	{
		if (!isset($function->method) OR !isset($function->parameters)) return false;

		$auth = static::configAuth();
		if (substr($function->parameters, 0, 6) == "array(") {

			$function->parameters = explode(',', $function->parameters);
			for ($p = 0; $p < count($function->parameters); $p++) {
				$function->parameters[$p] = str_replace("'", '', $function->parameters[$p]);
				$function->parameters[$p] = str_replace('array(', '', $function->parameters[$p]);
				$function->parameters[$p] = str_replace(')', '', $function->parameters[$p]);
			}

			return call_user_func_array($auth->class.'::'.$function->method, $function->parameters);
		} else {
			return call_user_func($auth->class.'::'.$function->method, $function->parameters);
		}
	}

}