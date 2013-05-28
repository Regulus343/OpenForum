<?php namespace Regulus\OpenForum;

use \BaseController;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

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

		$messages['info'] = Format::pluralize('Displaying <strong>[number]</strong> [word] in <strong>'.$section->title.'</strong>.', count($section->threads), 'thread');

		return View::make(Config::get('open-forum::viewsLocation').'section')
			->with('section', $section)
			->with('threads', $section->threads)
			->with('messages', $messages);
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

		Site::addTrailItem($section->title, 'forum/'.$section->slug);

		Site::set('title', 'Forum: Create Thread in '.$section->title.' Section');
		Site::addTrailItem('Create Thread', 'forum/thread/create/'.$section->slug);

		return View::make(Config::get('open-forum::viewsLocation').'create')->with('section', $section);
	}

	public function postCreateThread($slug = 'general')
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
	}


	/*public function getEditCreateThread($id = 'general')
	{
		Site::set('forumSection', OpenForum::section($id));

		$thread = array();
		if ($id != "") {
			if (is_int($id)) {
				$thread = ForumThread::find($id);
				if (empty($thread)) $id = "";
				$section = ForumSection::bySlug($thread->slug);
			} else {
				$section = ForumSection::bySlug($id);
			}
		}

		if (empty($section))
			return Redirect::to('forum/'.$section->slug)
				->with('messageError', Lang::get('open-forum::messages.errorSectionNotExistent'));

		if (Auth::guest()) {
			return Redirect::to('forum/'.$section->slug)
				->with('messageError', Lang::get('You cannot interact on the forum unless you :linkLogIn or :linkCreateAccount.', array('linkLogIn' => '<a href="'.URL::to('login').'">log in</a>', 'linkCreateAccount' => '<a href="'.URL::to('signup').'">create an account</a>'));
		}

		Site::set('subSection', 'Forum: '.$section->title);

		Site::addTrailItem($section->title, 'forum/'.$section->slug);
		if (!empty($thread)) {
			Site::set('title', 'Forum: '.$thread->title.' (Edit)');
			$this->layout->add_trail($this->data['thread']->title, 'forum/thread/'.$this->data['section_uri']);
		} else {
			$this->config->set_item('title', 'Forum: New Thread');
			$this->layout->add_trail('New Thread', 'forum/thread/'.$this->data['section_uri']);
		}

		$this->data['post_content'] = $this->general->purify_html($this->input->post('content'));

		$this->form_validation->set_rules('thread_title', 'Title', 'trim|required|min_length[3]');
		$this->form_validation->set_rules('content', 'Content', 'trim|required|min_length[24]');
		if ($this->input->post('preview') == '1') {
			$this->form_validation->set_rules('prevent_validation', 'Prevent Validation', 'required');
		}
		if ($this->form_validation->run()) {
			$thread_id = $this->forum->create_thread($this->data['forum_section']->uri_tag);
			if ($thread_id) {
				flash('success', 'You have successfully created a thread in <a href="'.site_url('forum/'.$this->data['section_uri']).'" style="font-weight: bold;">'.$this->data['forum_section']->title.'</a>.',
					  'forum/'.$thread_id);
			} else {
				$this->data['messages']['error'] = 'Something went wrong.';
				$this->data['messages']['error_sub'] = 'Please correct any errors and try again.';
			}
		} else {
			if ($_POST) {
				if ($this->input->post('preview') == '1') {
					$this->data['messages']['success'] = 'You are previewing your unpublished thread.';
					$this->data['messages']['success_sub'] = 'Your thread will not be saved until you click the <strong>Create Thread</strong> button. You may make changes and preview as often as you like, but please remember to actually create your thread when you are done making changes.';
				} else {
					$this->data['messages']['error'] = 'Something went wrong.';
					$this->data['messages']['error_sub'] = 'Please correct any errors and try again.';
				}
			}
		}

		$this->load->view('forum/thread_add', $this->data);
	}

	public function thread_view($id='')
	{
		$this->data['thread'] = $this->forum->thread($id);
		if (empty($this->data['thread'])) flash('error', 'The selected thread was not found.', 'forum');
		$this->data['thread_id'] = $id;
		$this->data['forum_section'] = $this->forum->section($this->data['thread']->section_id);

		$this->data['posts'] = $this->forum->posts($this->data['thread_id']);
		if (empty($this->data['posts'])) flash('error', 'The selected thread was not found.', 'forum');
		$this->data['id'] = $id;
		$this->data['section_uri'] = $this->data['forum_section']->uri_tag;

		$this->config->set_item('sub_section', 'Forum: '.$this->data['forum_section']->title);
		$this->layout->add_trail($this->data['forum_section']->title, 'forum/'.$this->data['section_uri']);
		$this->config->set_item('title', $this->data['thread']->title);
		$this->layout->add_trail($this->data['thread']->title, 'forum/'.$id);

		$this->data['messages']['success'] = pluralize('Displaying <strong>[number]</strong> [word].', count($this->data['posts']), 'post');

		$this->load->view('forum/thread', $this->data);
	}

	public function add_post()
	{
		$result = $this->forum->add_update_post();
		if ($result['result'] == "Error") {
			set_cookie(array('name'=>	'post_incomplete',
							 'value'=>	$this->general->purify_html($this->input->post('content')),
							 'expire'=>	12000));
		} else {
			delete_cookie('comment_incomplete');
		}
		set_cookie(array('name'=>	'post_id_actioned',
						 'value'=>	$result['post_id'],
						 'expire'=>	12000));

		flash(strtolower($result['result']), $result['message'], $result['return']);
	}*/

}