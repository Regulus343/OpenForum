<ul id="forum-nav">
	@if (Regulus\OpenForum\OpenForum::auth())

		@if (isset($section) && !empty($section) && (Regulus\OpenForum\OpenForum::admin() || !$section->new_thread_admin))

			<li>
				<a href="{{ URL::to('forum') }}" class="button">
					<div class="icon icon-arrow-left"></div>Return to Sections Menu
				</a>
			</li>
			<li>
				<a href="{{ URL::to('forum/thread/'.$section->slug) }}" class="button">
					<div class="icon icon-th-list"></div>Create New Thread
				</a>
			</li>

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
</ul>