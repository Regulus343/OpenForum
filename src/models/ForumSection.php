<?php namespace Regulus\OpenForum;

use Illuminate\Database\Eloquent\Model as Eloquent;

class ForumSection extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'forum_sections';

	/**
	 * Turn off timestamps.
	 *
	 * @var string
	 */
	public $timestamps = false;

}