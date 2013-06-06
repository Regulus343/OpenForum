<?php namespace Regulus\OpenForum;

use Illuminate\Database\Eloquent\Model as Eloquent;

use Illuminate\Support\Facades\Request;

class ForumThreadView extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var    string
	 */
	protected $table = 'forum_thread_views';

	/**
	 * The attributes that cannot be updated.
	 *
	 * @var    array
	 */
	protected $guarded = array('id');

	/**
	 * Gets the user of the thread view.
	 *
	 * @return object
	 */
	public function user()
	{
		return $this->belongsTo(Config::get('auth.model'), 'user_id');
	}

	/**
	 * The thread that the view belongs to.
	 *
	 * @var    object
	 */
	public function thread()
	{
		return $this->belongsTo('Regulus\OpenForum\ForumThread', 'thread_id');
	}

}