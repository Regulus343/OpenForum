@extends(Config::get('open-forum::layout'))

@section(Config::get('open-forum::section'))

	@include('open-forum::partials.included_files')

	@include('open-forum::partials.messages')

	@include('open-forum::partials.nav')

	<div class="create-post" id="create-forum-thread">
		{{ Form::open() }}

			<fieldset>
				<legend>{{ Lang::get('open-forum::labels.createThread') }}</legend>

				{{-- Title --}}
				<div class="field-row">
					{{ Form::field('title', null, array('placeholder' => Lang::get('open-forum::labels.addThreadTitlePlaceholder'))) }}
				</div>
				<div class="clear"></div>

				{{-- Content --}}
				<div class="field-row">
					{{ Form::field('content', 'textarea', array('id-field' => 'new-thread-post-content', 'class-field' => 'wysiwyg', 'placeholder' => Lang::get('open-forum::labels.addPostContentPlaceholder'), 'value' => '')) }}
				</div>
				<div class="clear"></div>

				{{-- Preview --}}
				{{ Form::hidden('preview', null, array('id-field' => 'preview-thread')) }}

			</fieldset>

			{{-- Preview & Create Thread --}}
			<div class="padded-vertical fieldset-indent">
				{{ Form::button(Lang::get('open-forum::labels.previewThread'), array('id' => 'btn-preview-thread')) }}
				{{ Form::button(Lang::get('open-forum::labels.createThread'), array('id' => 'btn-create-thread')) }}
			</div>

		{{ Form::close() }}
	</div><!-- /#create-thread -->

@stop