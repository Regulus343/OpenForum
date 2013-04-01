<?php namespace Regulus\OpenForum;

use Illuminate\Database\Eloquent\Model as Eloquent;

class ForumThread extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'forum_threads';

	/**
	 * The attributes that cannot be updated.
	 *
	 * @var array
	 */
	protected $guarded = array('id');

	public function section()
	{
		return $this->belongsTo('ForumSection');
	}

	public function posts()
	{
		return $this->hasMany('ForumPost', 'thread_id');
	}

}