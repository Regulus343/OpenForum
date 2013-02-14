@extends(Config::get('open-forum::layout'))

@section(Config::get('open-forum::section'))

	<p>{{{ $section->description }}</p>

	@include('open-forum::common.messages')

	@if (!empty($forum_threads))

		<ul class="content" id="forum-threads">
			@foreach ($forum_threads as $thread)

				<li class="full-link">
					<a href="{{{ URL::to('forum/'.$thread->id) }}" class="full-link"></a>

					<h1><?=$thread->title?></h1>

					<ul class="info">
						<li><label>Creator:</label> <span><a href="javascript:void(0);" class="view-user-profile" rel="u<?=$thread->user_id?>"><?=$thread->user?></a></span></li>
						<li><label>Replies:</label> <span>{{{ ($thread->posts - 1) }}</span></li>
						<li><label>Views:</label> <span>{{{ $thread->views }}</span></li>
						@if ($thread->latest_post_username)
							<li>
								<label>Latest Post:</label>
								<a href="{{{ URL::to('forum/'.$thread->latest_post_thread_id.'#post'.$thread->latest_post_id) }}">
									<?=date('M j, Y \a\t g:ia', strtotime($thread->date_latest_post))?>
								</a> by
								<a href="javascript:void(0);" class="view-user-profile" rel="{{{ $thread->latest_post_user_id }}">{{{ $thread->latest_post_user }}</a>
							</li>
						@endif
					</ul>

					<p>{{{ character_limiter(strip_tags($thread->content), 360, '...') }}</p>
				</li>

			@endforeach
		</ul>
		<a href="#" class="back-to-top">back to top</a>

	@endif

@stop