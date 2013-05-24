@extends(Config::get('open-forum::layout'))

@section(Config::get('open-forum::section'))

	@include('open-forum::partials.included_files')

	<p>The forum is divided into <strong>{{ count($sections) }}</strong> different sections. You may select a section below to see the threads it contains.</p>

	@include('open-forum::partials.nav')

	@include('open-forum::partials.messages')

	<ul class="content" id="forum-sections">
		@foreach ($sections as $section)

			<li class="full-link">
				<a href="{{ URL::to('forum/'.$section->slug) }}" class="full-link"></a>

				<h1><?=$section->title?></h1>

				<ul class="info">
					<li><label>Threads:</label> <span>{{ $section->threads }}</span></li>
					<li><label>Posts:</label> <span>{{ $section->posts }}</span></li>
					@if ($section->latest_post_username)
						<li>
							<label>Latest Post:</label>
							<span>
								<a href="{{{ URL::to('forum/'.$section->latest_post_thread_id.'#post'.$forumSection->latest_post_id) }}}">
									{{ date('M j, Y \a\t g:ia', strtotime($section->date_latest_post)) }}
								</a> by
								<a href="{{{ URL::to('member/'.$section->latest_post_username) }}}">{{ $section->latest_post_user }}</a>
							</span>
						</li>
					@endif
				</ul>

				<p>{{ Format::paragraphs($section->description) }}</p>
			</li>

		@endforeach
	</ul>

@stop