<?php namespace Regulus\OpenForum;

use Illuminate\Database\Eloquent\Model as Eloquent;

class ForumSection extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var    string
	 */
	protected $table = 'forum_sections';

	/**
	 * Turn off timestamps.
	 *
	 * @var    boolean
	 */
	public $timestamps = false;

	/**
	 * The attributes that cannot be updated.
	 *
	 * @var    array
	 */
	protected $guarded = array('id');

	/**
	 * The threads that belong to the section.
	 *
	 * @var    object
	 */
	public function threads()
	{
		return $this->hasMany('Regulus\OpenForum\ForumThread', 'section_id')->orderBy('updated_at', 'desc');
	}

	/**
	 * The number of posts that belong to the section.
	 *
	 * @var    object
	 */
	public function getNumberOfPosts()
	{
		$posts = 0;
		foreach ($this->threads as $thread) {
			$posts += $thread->posts->count();
		}
		return $posts;
	}

	/**
	 * Get a section by its URI slug.
	 *
	 * @return object
	 */
	public static function bySlug($slug = '')
	{
		return static::where('slug', '=', $slug)->first();
	}

	/**
	 * Get the latest post for the section.
	 *
	 * @return object
	 */
	public function getLatestPost()
	{
		return ForumPost::where('section_id', '=', $this->id)->orderBy('id', 'desc')->first();
	}

}