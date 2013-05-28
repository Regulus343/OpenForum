<?php namespace Regulus\OpenForum;

use Illuminate\Database\Eloquent\Model as Eloquent;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;

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

		//require minimum length
		if (Config::get('open-forum::postMinLength') && strlen($content) < Config::get('open-forum::postMinLength')) {
			$results['message'] = Lang::get('open-forum::messages.errorMinLength', array('number' => Config::get('open-forum::postMinLength')));
			return $results;
		}

		$admin = OpenForum::admin();

		if ($id) {
			$results['action'] = "Update";

			//if editing, ensure user has sufficient privileges to edit
			if (!$admin) {
				$postEditable = ForumPost::where('id', '=', $id)
										->where('user_id', '=', OpenForum::userID())
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

			$post = new static;

			$post->user_id = $userID;

			$autoApproval = Config::get('open-forum::postAutoApproval');
			if ($autoApproval || $admin) {
				$post->approved    = true;
				$post->approved_at = date('Y-m-d H:i:s');
			}
		}

		if ($results['action'] == "Create") {
			$thread = new ForumThread;
			$thread->slug  = Format::uniqueSlug($title, 'forum_threads', 64);
			$thread->title = $title;
			$thread->save();

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
			if (!$autoApproval) $results['message'] .= ' '.Lang::get('open-forum::messages.notYetApproved');
		} else {
			$results['message'] = Lang::get('open-forum::messages.successThreadUpdated');
		}

		//log activity
		//Activity::log(ucwords($data['content_type']).' - Comment Updated', '', $data['content_id']);

		return $results;
	}

}