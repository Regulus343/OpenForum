<?php namespace Regulus\OpenForum;

use Illuminate\Database\Eloquent\Model as Eloquent;

class ForumPost extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'forum_posts';

	public function section()
	{
		return $this->belongsTo('ForumThread', 'thread_id');
	}

	public function user()
	{
		return $this->hasOne('users', 'user_id');
	}

}