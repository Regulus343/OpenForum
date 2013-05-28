@extends(Config::get('open-forum::layout'))

@section(Config::get('open-forum::section'))

	@include('open-forum::partials.included_files')

	@include('open-forum::partials.messages')

	@include('open-forum::partials.nav')

	<div class="create-post" id="create-thread">
		{{ Form::open() }}

			<fieldset>
				<legend>{{ Lang::get('open-forum::labels.createThread') }}</legend>

				{{-- Title --}}
				<div class="field-row">
					{{ Form::field('title') }}
				</div>
				<div class="clear"></div>

				{{-- Content --}}
				<div class="field-row">
					{{ Form::field('content', 'textarea', array('id-field' => 'post-content', 'class-field' => 'wysiwyg', 'placeholder' => Lang::get('open-forum::labels.addPostContentPlaceholder'))) }}
				</div>
				<div class="clear"></div>

				{{-- Preview --}}
				{{ Form::hidden('preview', null, array('id-field' => 'preview-thread')) }}

			</fieldset>

			{{-- Preview & Create Thread --}}
			<div class="padded-vertical fieldset-indent">
				{{ Form::submit(Lang::get('open-forum::labels.previewThread')) }}
				{{ Form::submit(Lang::get('open-forum::labels.createThread')) }}
			</div>

		{{ Form::close() }}
	</div><!-- /#create-thread -->

@stop