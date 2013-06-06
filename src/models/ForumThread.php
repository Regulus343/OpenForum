<?php namespace Regulus\OpenForum;

use Illuminate\Database\Eloquent\Model as Eloquent;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

use Regulus\TetraText\TetraText as Format;

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
	 * Gets the creator of the thread.
	 *
	 * @return object
	 */
	public function creator()
	{
		return $this->belongsTo(Config::get('auth.model'), 'user_id');
	}

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
		return $this->hasMany('Regulus\OpenForum\ForumPost', 'thread_id')->orderBy('id');
	}

	/**
	 * The views that belong to the thread.
	 *
	 * @var    object
	 */
	public function views()
	{
		return $this->hasMany('Regulus\OpenForum\ForumThreadView', 'thread_id')->orderBy('id', 'desc');
	}

	/**
	 * The first post of the thread.
	 *
	 * @var    object
	 */
	public function getFirstPost()
	{
		return $this->posts->first();
	}

	/**
	 * The latest post of the thread.
	 *
	 * @var    object
	 */
	public function getLatestPost()
	{
		return $this->posts->last();
	}

	/**
	 * Creates or updates a thread.
	 *
	 * @param  integer  $id
	 * @return mixed
	 */
	public static function createUpdate($id = 0)
	{
		$results = array(
			'resultType' => 'Error',
			'action'     => 'Create',
			'threadID'   => false,
			'threadSlug' => '',
			'postID'     => false,
			'content'    => '',
			'message'    => Lang::get('open-forum::messages.errorGeneral'),
		);

		//ensure user is logged in
		if (!OpenForum::auth()) return $results;

		$userID    = OpenForum::userID();
		$id        = (int) trim(Input::get('thread_id'));
		$sectionID = (int) trim(Input::get('section_id'));
 		$title     = ucfirst(trim(Input::get('title')));
		$content   = Format::purifyHTML(Input::get('content'));
		$editLimit = date('Y-m-d H:i:s', strtotime('-'.Config::get('open-forum::editLimit').' minutes'));
		$admin     = OpenForum::admin();

		//require minimum length
		if (Config::get('open-forum::postMinLength') && strlen($content) < Config::get('open-forum::postMinLength')) {
			$results['message'] = Lang::get('open-forum::messages.errorMinLength', array('number' => Config::get('open-forum::postMinLength')));
			return $results;
		}

		if ($id) {
			$results['action'] = "Update";

			//if editing, ensure user has sufficient privileges to edit
			if (!$admin) {
				$postEditable = ForumPost::where('id', '=', $id)
										->where('user_id', '=', $userID)
										->where('created_at', '>=', $editLimit)
										->count();
				if (!$postEditable) {
					$results['message'] = Lang::get('open-forum::messages.errorUneditable');
					return $results;
				}
			}

			if ($admin) {
				$thread = static::find($id);
			} else {
				$thread = static::where('id', '=', $id)->where('user_id', '=', $userID)->first();
			}

			if (empty($thread)) return $results;

		} else {

			//ensure section exists
			$section = ForumSection::find($sectionID);
			if (empty($section))
				return $results;

			//ensure posted section is not admin only
			if (!$admin && $section->admin_create_thread)
				return $results;

			//ensure user has not posted a thread or reply too soon after another one
			if (!$admin) {
				$postWaitTime = Config::get('open-forum::postWaitTime');
				if ($postWaitTime) {
					$lastPost = ForumPost::where('user_id', '=', $userID)->orderBy('id', 'desc')->first();
					if (!empty($lastPost)) {
						$timeToWait = $postWaitTime - (time() - strtotime($lastPost->created_at));
						if ($timeToWait > 0) {
							$results['message'] = Lang::get('open-forum::messages.errorWaitTime', array('number' => $postWaitTime, 'time' => $timeToWait, 'secondPlural' => 'second'.($postWaitTime == 1 ? '' : 's')));
							return $results;
						}
					}
				}
			}
		}

		if ($results['action'] == "Create") {
			$thread = new ForumThread;
			$thread->user_id    = $userID;
			$thread->section_id = $sectionID;
			$thread->slug       = Format::uniqueSlug($title, 'forum_threads', 64);
			$thread->title      = $title;
			$thread->save();

			//add initial view by creating user
			$thread->recordView();

			$post = new ForumPost;
			$post->user_id    = $userID;
			$post->thread_id  = $thread->id;
			$post->ip_address = Request::getClientIp();
		}

		$post->content = $content;
		$post->save();

		$results['threadID']   = $thread->id;
		$results['threadSlug'] = $thread->slug;
		$results['postID']     = $post->id;

		if ($results['action'] == "Create")
			Session::set('lastPost', $post->id);

		$results['resultType'] = "Success";
		if ($results['action'] == "Create") {
			$results['message'] = Lang::get('open-forum::messages.successThreadCreated');
		} else {
			$results['message'] = Lang::get('open-forum::messages.successThreadUpdated');
		}

		//log activity
		//Activity::log(ucwords($data['content_type']).' - Comment Updated', '', $data['content_id']);

		return $results;
	}

	/**
	 * Formats the threads for Handlebars JS.
	 *
	 * @param  object   $threads
	 * @return mixed
	 */
	public static function format($threads)
	{
		$threadsFormatted = array();

		$admin = OpenForum::admin();

		if (OpenForum::auth()) {
			$user = OpenForum::user();
			$activeUser = array(
				'id'           => $user->id,
				'name'         => $user->getName(),
				'role'         => 'User',
				'member_since' => date('F Y', strtotime($user->activated_at)),
				'image'        => $user->getPicture(),
			);
		} else {
			$activeUser = array(
				'id'           => 0,
				'name'         => '',
				'role'         => '',
				'member_since' => '',
				'image'        => '',
			);
		}

		foreach ($threads as $thread) {
			$threadArray = $thread->toArray();

			$threadArray['logged_in'] = OpenForum::auth();

			$creator                  = $thread->creator;
			$threadArray['user']      = $creator->getName();
			$threadArray['replies']   = count($thread->posts) - 1;
			$threadArray['views']     = count($thread->views);

			$latestPost = $thread->getLatestPost();

			$threadArray['latest_post_id']      = $latestPost->id;
			$threadArray['date_latest_post']    = date('F j, Y', strtotime($latestPost->created_at));
			$threadArray['latest_post_user_id'] = $latestPost->creator->id;
			$threadArray['latest_post_user']    = $latestPost->creator->getName();

			$threadArray['created_at'] = date('F j, Y \a\t g:i:sa', strtotime($threadArray['created_at']));
			$threadArray['updated_at'] = date('F j, Y \a\t g:i:sa', strtotime($threadArray['updated_at']));
			if (substr($threadArray['created_at'], 0, 13) != substr($threadArray['updated_at'], 0, 13)) {
				$threadArray['updated'] = true;
			} else {
				$threadArray['updated'] = false;
			}

			$threadArray['content']   = Format::charLimit($thread->getFirstPost()->content, 360, '...', false, true);

			$threadArray['edit_time'] = strtotime($thread->created_at) - strtotime('-'.Config::get('open-forum::commentEditLimit').' seconds');

			if ($threadArray['edit_time'] < 0)
				$threadArray['edit_time'] = 0;

			if (Session::get('lastPost') != $threadArray['id'] || $admin)
				$threadArray['edit_time'] = 0;

			if ($threadArray['edit_time'] || $admin) {
				$threadArray['edit'] = true;
			} else {
				$threadArray['edit'] = false;
			}

			$threadArray['deleted'] = (bool) $threadArray['deleted'];

			if ($threadArray['user_id'] == $activeUser['id']) {
				$threadArray['active_user_post'] = true;
			} else {
				$threadArray['active_user_post'] = false;
			}

			$threadsFormatted[] = $threadArray;
		}
		return $threadsFormatted;
	}

	/**
	 * Record a view if user ID or IP address has not already viewed thread.
	 *
	 * @var    object
	 */
	public function recordView()
	{
		$userID    = OpenForum::userID();
		$ipAddress = Request::getClientIp();

		$exists = ForumThreadView::where('thread_id', '=', $this->id)->where(function($query)
		{
			$query
				->where('user_id', '=', $userID)
				->orWhere('ip_address', '=', $ipAddress);
		})->count();

		if (!$exists) {
			$view = new ForumThreadView;
			$view->thread_id  = $this->id;
			$view->user_id    = $userID;
			$view->ip_address = $ipAddress;
			$view->save();
		}
	}

}