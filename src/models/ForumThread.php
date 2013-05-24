<?php namespace Regulus\OpenForum;

use Illuminate\Database\Eloquent\Model as Eloquent;

class ForumThread extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var    string
	 */
	protected $table = 'forum_threads';

	/**
	 * The attributes that cannot be updated.
	 *
	 * @var    array
	 */
	protected $guarded = array('id');

	/**
	 * The section that the thread belongs to.
	 *
	 * @var    object
	 */
	public function section()
	{
		return $this->belongsTo('Regulus\OpenForum\ForumSection', 'section_id');
	}

	/**
	 * The posts that belong to the thread.
	 *
	 * @var    object
	 */
	public function posts()
	{
		return $this->hasMany('Regulus\OpenForum\ForumPost', 'thread_id');
	}

}