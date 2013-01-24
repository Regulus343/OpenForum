<?php namespace Regulus\OpenForum;

/*----------------------------------------------------------------------------------------------------------
	OpenForum
		A light, effective discussion forum composer package that is easy to configure and implement.

		created by Cody Jassman
		last updated on January 24, 2013
----------------------------------------------------------------------------------------------------------*/

class OpenForum {

	/**
	 * Get the list of available forum sections.
	 *
	 *
	 * @return array
	 */
	public static function getSections()
	{
		return ForumSection::all();
	}

	public static function getSection($id)
	{
		return ForumSection::where('id', '=', $id)->orWhere('uri_tag', '=', $id)->first();
	}

}