<?php namespace Regulus\OpenForum;

use \BaseController;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\URL;

use Aquanode\Formation\Formation as Form;
use Regulus\TetraText\TetraText as Format;
use Regulus\SolidSite\SolidSite as Site;

class ForumController extends BaseController {

	public function getIndex()
	{
		Site::setMulti(array('subSection', 'title'), 'Forum: Table of Contents');
		return View::make(Config::get('open-forum::viewsLocation').'home');
	}

	public function getHome()
	{
		Site::setMulti(array('subSection', 'title'), 'Forum: Table of Contents');
		return View::make(Config::get('open-forum::viewsLocation').'home');
	}

	public function getSection($slug = 'general')
	{
		$section = ForumSection::bySlug($slug);
		if (empty($section))
			return Redirect::to('forum')->with('messageError', Lang::get('open-forum::messages.errorSectionNotExistent'));

		Site::setMulti(array('subSection', 'title'), 'Forum: '.$section->title);

		Site::addTrailItem($section->title, 'forum/'.$slug);

		$threads = $section->threads;

		Form::setDefaults(array('section' => $section->slug));

		$messages['info'] = Format::pluralize('Displaying <strong>[number]</strong> [word] in <strong>'.$section->title.'</strong>.', count($threads), 'thread');

		return View::make(Config::get('open-forum::viewsLocation').'section')
			->with('section', $section)
			->with('threads', json_encode(ForumThread::format($threads)))
			->with('messages', $messages);
	}

	public function ajaxSection($slug = 'general')
	{
		return "Section...";
	}

	public function ajaxThread($id = 0)
	{
		return "Thread...";
	}

	public function getCreateThread($slug = 'general')
	{
		$section = ForumSection::bySlug($slug);
		if (empty($section))
			return Redirect::to('forum/'.$section->slug)
				->with('messageError', Lang::get('open-forum::messages.errorSectionNotExistent'));

		if (!OpenForum::auth())
			return Redirect::to('forum/'.$section->slug)
				->with('messageError', Lang::get('open-forum::messages.errorLogIn', array('linkLogIn' => '<a href="'.URL::to('login').'">log in</a>', 'linkCreateAccount' => '<a href="'.URL::to('signup').'">create an account</a>')));

		Site::set('subSection', 'Forum: Create Thread');
		Site::set('forumSection', $section->title);
		Site::set('forumSectionSlug', $section->slug);

		Site::addTrailItem($section->title, 'forum/'.$section->slug);

		Site::set('title', 'Forum: Create Thread in '.$section->title.' Section');
		Site::addTrailItem('Create Thread', 'forum/thread/create/'.$section->slug);

		$defaults = array('section_id' => $section->id);
		Form::setDefaults($defaults);

		return View::make(Config::get('open-forum::viewsLocation').'create')->with('section', $section);
	}

	public function postCreateThread($slug = 'general')
	{
		$results = array(
			'resultType' => 'Error',
			'message'    => Lang::get('open-forum::messages.errorGeneral'),
		);

		$section = ForumSection::bySlug($slug);
		if (empty($section))
			return Redirect::to('forum/'.$section->slug)
				->with('messageError', Lang::get('open-forum::messages.errorSectionNotExistent'));

		if (!OpenForum::auth())
			return Redirect::to('forum/'.$section->slug)
				->with('messageError', Lang::get('open-forum::messages.errorLogIn', array('linkLogIn' => '<a href="'.URL::to('login').'">log in</a>', 'linkCreateAccount' => '<a href="'.URL::to('signup').'">create an account</a>')));

		$rules = array(
			'title'      => array('required', 'min:3', 'unique:forum_threads'),
			'content'    => array('required', 'min:24'),
			'section_id' => array('required'),
		);

		$preview = (bool) Input::get('preview');

		//if user is previewing post, do not validate form
		if ($preview) {
			$rules['prevent_validation'] = array('required');
		}
		Form::setValidationRules($rules);

		$messages = array();
		if (Form::validated()) {
			$results = ForumThread::createUpdate();
			if ($results['threadID']) {
				$results['resultType']  = "Success";
				$results['message']     = Lang::get('open-forum::messages.successThreadCreated');
				$results['redirectURI'] = "forum/thread/".$results['threadSlug'];
			}
		} else {
			if ($preview) {
				$results['resultType'] = "Success: Preview";
				$results['message']    = Lang::get('open-forum::messages.successPreviewingThread');
				$results['messageSub'] = Lang::get('open-forum::messages.successPreviewingThreadSub', array('createThread' => Lang::get('open-forum::labels.createThread')));

				$post = "";
				$results['posts']      = array($post);
			} else {
				if (Form::error('title'))
					$results['messageSub'] = Form::errorMessage('title');

				if (Form::error('content'))
					$results['messageSub'] = Form::errorMessage('content');
			}
		}

		return json_encode($results);
	}

	public function getThread($slug = '', $page = 1)
	{
		$thread = ForumThread::bySlug($slug);
		if (empty($thread))
			return Redirect::to('forum')->with('messageError', Lang::get('open-forum::messages.errorThreadNotFound'));

		Site::set('subSection', 'Forum: View Thread');
		Site::set('title', $thread->title);

		Site::addTrailItem($thread->section->title, 'forum/'.$thread->section->slug);
		Site::addTrailItem(Format::charLimit($thread->title, 48), 'forum/thread/'.$slug);

		if (!$page || is_int($page)) $page = 1;

		$posts        = $thread->paginatePosts($page);
		$postsPerPage = Config::get('open-forum::postsPerPage');
		$totalPosts   = count($thread->posts);

		$postPlural = Lang::get('open-forum::labels.post');
		if ($totalPosts > 1) $postPlural = Str::plural($postPlural);

		$start = $page * $postsPerPage - $postsPerPage + 1;
		$end   = $start + $postsPerPage - 1;
		if ($end > $totalPosts) $end = $totalPosts;

		Form::setDefaults(array('thread_id' => $thread->id));

		$messages['info'] = Lang::get('open-forum::messages.numberItems', array('start' => $start, 'end' => $end, 'total' => $totalPosts, 'itemPlural' => $postPlural));

		return View::make(Config::get('open-forum::viewsLocation').'thread')
			->with('section', $thread->section)
			->with('thread', $thread)
			->with('posts', json_encode(ForumPost::format($posts)))
			->with('messages', $messages);
	}

	public function postPost()
	{
		$results = array(
			'resultType' => 'Error',
			'message'    => Lang::get('open-forum::messages.errorGeneral'),
		);

		$thread = ForumSection::bySlug(Input::get('thread_id'));
		if (empty($thread))
			return json_encode($results);

		if (!OpenForum::auth())
			return json_encode($results);

		$rules = array(
			'content'   => array('required', 'min:24'),
			'thread_id' => array('required'),
		);
		Form::setValidationRules($rules);

		$messages = array();
		if (Form::validated()) {
			$results = ForumPost::createUpdate();
			if ($results['threadID']) {
				$results['resultType']  = "Success";
				$results['message']     = Lang::get('open-forum::messages.successPostCreated');
			}
		} else {
			if (Form::error('content'))
				$results['messageSub'] = Form::errorMessage('content');
		}

		return json_encode($results);
	}

	/*public function postCreateThread($slug = 'general')
	{
		$section = ForumSection::bySlug($slug);

		if (empty($section))
			return Redirect::to('forum/'.$section->slug)
				->with('messageError', Lang::get('open-forum::messages.errorSectionNotExistent'));

		if (!OpenForum::auth())
			return Redirect::to('forum/'.$section->slug)
				->with('messageError', Lang::get('open-forum::messages.errorLogIn', array('linkLogIn' => '<a href="'.URL::to('login').'">log in</a>', 'linkCreateAccount' => '<a href="'.URL::to('signup').'">create an account</a>')));

		Site::set('subSection', 'Forum: Create Thread');
		Site::set('forumSection', $section->title);

		Site::addTrailItem($section->title, 'forum/'.$section->slug);

		Site::set('title', 'Forum: Create Thread in '.$section->title.' Section');
		Site::addTrailItem('Create Thread', 'forum/thread/create/'.$section->slug);

		$rules = array(
			'title'   => array('required', 'min:3'),
			'content' => array('required', 'min:24'),
		);

		$preview = (bool) Input::get('preview');

		//if user is previewing post, do not validate form
		if ($preview) {
			$rules['prevent_validation'] = array('required');
		}
		Form::setValidationRules($rules);

		$messages = array();
		if (Form::validated()) {
			$results = ForumThread::createUpdate();
			if ($results['threadID']) {
				return Redirect::to('forum/thread/'.$results['threadSlug'])
					->with('messageSuccess', $results['message']);
			} else {
				$messages = array('error' => $results['message']);
			}
		} else {
			if ($preview) {
				$messages = array(
					'success'    => Lang::get('open-forum::messages.successPreviewingThread'),
					'successSub' => Lang::get('open-forum::messages.successPreviewingThreadSub', array('createThread' => Lang::get('open-forum::labels.createThread'))),
				);
			} else {
				$messages = array('error' => Lang::get('open-forum::messages.errorGeneral'));
			}
		}

		return View::make(Config::get('open-forum::viewsLocation').'create')
			->with('section', $section)
			->with('messages', $messages);
	}*/

}