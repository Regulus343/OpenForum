<?php namespace Regulus\OpenForum;

use Illuminate\Database\Eloquent\Model as Eloquent;

class ForumThread extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'forum_threads';

	public function section()
	{
		return $this->belongsTo('ForumSection', 'section_id');
	}

	public function posts()
	{
		return $this->hasMany('ForumPost', 'thread_id');
	}

}