<ul id="forum-nav">
	@if (Site::get('subSection') == "Forum: Create Thread" || Site::get('subSection') == "Forum: View Thread")
		<li>
			<a href="{{ URL::to('forum/'.$section->slug) }}" class="button">
				<div class="icon icon-arrow-left"></div>Return to Threads in {{ $section->title }}
			</a>
		</li>
	@else
		@if (isset($section) && !empty($section))
			<li>
				<a href="{{ URL::to('forum') }}" class="button">
					<div class="icon icon-arrow-left"></div>Return to Sections Menu
				</a>
			</li>

			@if (Regulus\OpenForum\OpenForum::auth() && (Regulus\OpenForum\OpenForum::admin() || !$section->admin_create_thread))
				<li>
					<a href="{{ URL::to('forum/thread/create/'.$section->slug) }}" class="button">
						<div class="icon icon-th-list"></div>Create New Thread
					</a>
				</li>
			@endif
		@endif
	@endif

	@if (Regulus\OpenForum\OpenForum::auth() && isset($posts) && !empty($posts))
		<li>
			<a href="" class="button button-reply">
				<div class="icon icon-th-list"></div>Reply to Current Thread
			</a>
		</li>
	@endif

	@if (!Regulus\OpenForum\OpenForum::auth())
		<p>
			<a href="{{ URL::to('login') }}">Log in</a> or <a href="{{ URL::to('signup') }}">sign up</a> to interact with the community.
		</p>
	@endif
</ul><!-- /#forum-nav -->
<div class="clear"></div>

@if (isset($sections) && !empty($sections))
	{{ Form::select('section', Form::prepOptions($sections, array('slug', 'title')), Lang::get('open-forum::labels.navigateToSection'), null, array('id' => 'select-forum-section')) }}
	<div class="clear"></div>
@endif