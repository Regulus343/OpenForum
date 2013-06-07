<?php namespace Regulus\OpenForum;

use Illuminate\Database\Eloquent\Model as Eloquent;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

use Regulus\TetraText\TetraText as Format;

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
	 * The default order of the posts, "asc" being oldest to newest and
	 * "desc" being newest to oldest.
	 *
	 * @var string
	 */
	public static $order = false;

	/**
	 * Gets the creator of the post.
	 *
	 * @return object
	 */
	public function creator()
	{
		return $this->belongsTo(Config::get('auth.model'), 'user_id');
	}

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
	 * Creates or updates a post.
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
		$id        = (int) trim(Input::get('post_id'));
		$threadID  = (int) trim(Input::get('thread_id'));
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
		} else {

			//ensure section exists
			$section = ForumSection::find($sectionID);
			if (empty($section))
				return $results;

			//ensure user has not posted a thread or reply too soon after another one
			if (!$admin) {
				$postWaitTime = Config::get('open-forum::postWaitTime');
				if ($postWaitTime) {
					$lastPost = static::where('user_id', '=', $userID)->orderBy('id', 'desc')->first();
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

		if ($admin) {
			$thread = ForumThread::find($threadID);
		} else {
			$thread = ForumThread::where('id', '=', $threadID)->where('user_id', '=', $userID)->first();
		}
		if (empty($thread)) return $results;

		if ($results['action'] == "Create") {
			$post = new ForumPost;
			$post->user_id    = $userID;
			$post->section_id = $sectionID;
			$post->thread_id  = $threadID;
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
			$results['message'] = Lang::get('open-forum::messages.successPostCreated');
		} else {
			$results['message'] = Lang::get('open-forum::messages.successPostUpdated');
		}

		//log activity
		//Activity::log(ucwords($data['content_type']).' - Comment Updated', '', $data['content_id']);

		return $results;
	}

	/**
	 * Formats the posts for Handlebars JS.
	 *
	 * @param  object   $posts
	 * @return mixed
	 */
	public static function format($posts)
	{
		$postsFormatted = array();

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

		foreach ($posts as $post) {
			$postArray = $post->toArray();

			$postArray['logged_in'] = OpenForum::auth();

			$creator                    = $post->creator;
			$postArray['user']          = $creator->getName();
			$postArray['user_role']     = $creator->roles[0]->name;
			$postArray['user_comments'] = 0;
			$postArray['user_since']    = date('F Y', strtotime($creator->activated_at));
			$postArray['user_image']    = $post->creator->getPicture();

			$postArray['created_at'] = date('F j, Y \a\t g:i:sa', strtotime($postArray['created_at']));
			$postArray['updated_at'] = date('F j, Y \a\t g:i:sa', strtotime($postArray['updated_at']));
			if (substr($postArray['created_at'], 0, 13) != substr($postArray['updated_at'], 0, 13)) {
				$postArray['updated'] = true;
			} else {
				$postArray['updated'] = false;
			}

			$postArray['deleted'] = (bool) $postArray['deleted'];

			$postArray['edit_time'] = strtotime($post->created_at) - strtotime('-'.Config::get('open-forum::postEditLimit').' seconds');

			if ($postArray['edit_time'] < 0)
				$postArray['edit_time'] = 0;

			if (Session::get('lastPost') != $postArray['id'] || $admin)
				$postArray['edit_time'] = 0;

			if ((int) $postArray['user_id'] == (int) $activeUser['id']) {
				$postArray['active_user_post'] = false;
			} else {
				$postArray['active_user_post'] = false;
				$postArray['edit_time']        = 0;
			}

			if ($postArray['edit_time'] || $admin) {
				$postArray['edit'] = true;
			} else {
				$postArray['edit'] = false;
			}

			$postArray['edit_countdown'] = $postArray['edit_time'] > 0 ? Lang::get('open-forum::messages.editCountdown', array('seconds' => $postArray['edit_time'])) : '';

			$postArray['active_user_name']  = $activeUser['name'];
			$postArray['active_user_role']  = $activeUser['role'];
			$postArray['active_user_since'] = $activeUser['member_since'];
			$postArray['active_user_image'] = $activeUser['image'];

			$postsFormatted[] = $postArray;
		}
		return $postsFormatted;
	}

}