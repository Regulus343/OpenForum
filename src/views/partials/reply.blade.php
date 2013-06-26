@if (Regulus\OpenForum\OpenForum::auth())

	<div class="create-post hidden" id="reply-forum-thread">
		{{ Form::open() }}

			<fieldset>
				<legend>{{ Lang::get('open-forum::labels.reply') }}</legend>

				{{-- Content --}}
				<div class="field-row">
					{{ Form::textarea('content', null, array('id' => 'post-content', 'class' => 'wysiwyg', 'placeholder' => Lang::get('open-forum::labels.addPostContentPlaceholder'))) }}
					<div class="clear"></div>
				</div>

				{{-- Thread ID --}}
				{{ Form::hidden('thread_id') }}

				{{-- Add Reply --}}
				<div class="field-row">
					{{ Form::button(Lang::get('open-forum::labels.addReply'), array('id' => 'btn-reply-thread')) }}
				</div>
			</fieldset>

		{{ Form::close() }}
	</div><!-- /#create-thread -->

@endif