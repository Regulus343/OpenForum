<?php namespace Regulus\OpenForum;

use \BaseController;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;

use Regulus\TetraText\TetraText as Format;
use Regulus\SolidSite\SolidSite as Site;

class ForumController extends BaseController {

	public function getIndex()
	{
		Site::set('subSection', 'Forum: Table of Contents');
		Site::set('title', 'Forum: Table of Contents');
		return View::make('open-forum::home');
	}

	public function getHome()
	{
		Site::set('subSection', 'Forum: Table of Contents');
		Site::set('title', 'Forum: Table of Contents');
		return View::make('open-forum::home');
	}

	public function getSection($uri_tag = 'general') {
		$section = ForumSection::where('uri_tag', '=', $uri_tag)->first();
		if (empty($section)) {
			return Redirect::to('forum')->with('messagesError', 'The section you requested does not exist.');
		}
		Site::set('forumSectionURI', $section->uri_tag);
		Site::set('subSection', 'Forum: '.$section->title);
		Site::set('title', 'Forum: '.$section->title);

		Site::addTrailItem($section->title, 'forum/'.$uri_tag);

		$threads = ForumThread::where('section_id', '=', $section->id)->orderBy('id')->get();

		$messages['info'] = Format::pluralize('Displaying <strong>[number]</strong> [word] in <strong>'.$section->title.'</strong>.', count($threads), 'thread');

		return View::make('open-forum::section')->with('section', $section)->with('threads', $threads);
	}

	/*public function thread($id='general') {
		$this->data['forum_section'] = $this->forum->section($id);
		if (!empty($id)) {
			$id = "";
		} else {
			$this->data['thread'] = $this->forum->thread($id);
			if (empty($this->data['thread'])) $id = "";
			$this->data['forum_section'] = $this->forum->section($this->data['thread']->section_uri);
		}
		if (empty($this->data['forum_section'])) redirect('forum');
		$this->data['id'] = $id;
		$this->data['section_uri'] = $this->data['forum_section']->uri_tag;

		if (!$this->auth->active()) {
			flash('error', 'You cannot interact on the forum unless you <a href="'.site_url('login').'">log in</a> or <a href="'.site_url('signup').'">create an account</a>.', 'forum/'.$this->data['section_uri']);
		}

		$this->config->set_item('sub_section', 'Forum: '.$this->data['forum_section']->title);
		$this->layout->add_trail($this->data['forum_section']->title, 'forum/'.$this->data['section_uri']);
		if ($this->data['id'] != "") {
			$this->config->set_item('title', $this->config->item('sub_section'));
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

	public function thread_view($id='') {
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

	public function add_post() {
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