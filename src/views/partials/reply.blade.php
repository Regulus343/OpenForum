@if (Regulus\OpenForum\OpenForum::auth())

	<div class="create-post hidden" id="reply-forum-thread">
		{{ Form::open('forum/post', 'post', array('class' => 'form-post')) }}

			<fieldset>
				<legend>{{ Lang::get('open-forum::labels.reply') }}</legend>

				{{-- Content --}}
				<div class="field-row">
					{{ Form::textarea('content', null, array('id' => 'post-content', 'class' => 'wysiwyg', 'placeholder' => Lang::get('open-forum::labels.addPostContentPlaceholder'))) }}
					<div class="clear"></div>
				</div>

				{{-- Thread ID --}}
				{{ Form::hidden('thread_id') }}

				{{-- Post ID --}}
				{{ Form::hidden('post_id', 0) }}

				{{-- Add Reply --}}
				<div class="field-row">
					{{ Form::button(Lang::get('open-forum::labels.addReply'), array('id' => 'btn-reply-thread')) }}
				</div>
			</fieldset>

		{{ Form::close() }}
	</div><!-- /#create-thread -->

@endif