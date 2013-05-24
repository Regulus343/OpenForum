<?php namespace Regulus\OpenForum;

use Illuminate\Database\Eloquent\Model as Eloquent;

use Illuminate\Support\Facades\Config;

class ForumPost extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var    string
	 */
	protected $table = 'forum_posts';

	/**
	 * The attributes that cannot be updated.
	 *
	 * @var    array
	 */
	protected $guarded = array('id');

	/**
	 * The thread that the post belongs to.
	 *
	 * @var    object
	 */
	public function thread()
	{
		return $this->belongsTo('Regulus\OpenForum\ForumThread', 'thread_id');
	}

	/**
	 * The section that the post belongs to.
	 *
	 * @var    object
	 */
	public function section()
	{
		return $this->thread()->section();
	}

	/**
	 * The user that created the post.
	 *
	 * @var    object
	 */
	public function creator()
	{
		return $this->belongsTo(Config::get('auth.model'), 'user_id');
	}

}