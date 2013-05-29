<ul id="forum-nav">
	@if (Regulus\OpenForum\OpenForum::auth())

		@if (Site::get('subSection') == "Forum: Create Thread")
			<li>
				<a href="{{ URL::to('forum/'.$section->slug) }}" class="button">
					<div class="icon icon-arrow-left"></div>Return to Threads in {{ $section->title }}
				</a>
			</li>
		@else

			@if (isset($section) && !empty($section) && (Regulus\OpenForum\OpenForum::admin() || !$section->new_thread_admin))

				<li>
					<a href="{{ URL::to('forum') }}" class="button">
						<div class="icon icon-arrow-left"></div>Return to Sections Menu
					</a>
				</li>

				@if (Regulus\OpenForum\OpenForum::admin() || !$section->admin_create_thread)
					<li>
						<a href="{{ URL::to('forum/thread/create/'.$section->slug) }}" class="button">
							<div class="icon icon-th-list"></div>Create New Thread
						</a>
					</li>
				@endif
			@endif

		@endif

		@if (isset($posts) && !empty($posts))
			<li><a href="" class="button button-reply">
				<div class="icon icon-th-list"></div>Reply to Current Thread</a>
			</li>
		@endif

	@else
		<p>
			<a href="{{ URL::to('login') }}">Log in</a> or <a href="{{ URL::to('signup') }}">sign up</a> to interact with the community.
		</p>
	@endif
</ul><!-- /#forum-nav -->
<div class="clear"></div>