{{-- success messages --}}
@if (!is_null(Session::get('messageSuccess')) && Session::get('messageSuccess') != "")
	<div class="message success">
		<div>{{ Session::get('messageSuccess') }}</div>
	</div>
@endif

@if (isset($messages['success']) && $messages['success'] != "")
	<div class="message success">
		<div>{{ $messages['success'] }}</div>

		@if (isset($messages['successSub']))
			<div class="sub">{{ $messages['successSub'] }}</div>
		@endif
	</div>
@endif

{{-- error messages --}}
@if (!is_null(Session::get('messageError')) && Session::get('messageError') != "")
	<div class="message error">
		<div>{{ Session::get('messageError') }}</div>
	</div>
@endif

@if (isset($messages['error']) && $messages['error'] != "")
	<div class="message error">
		<div>{{ $messages['error'] }}</div>

		@if (isset($messages['errorSub']))
			<div class="sub">{{ $messages['errorSub'] }}</div>
		@endif
	</div>
@endif

{{-- general info messages --}}
@if (!is_null(Session::get('messageInfo')) && Session::get('messageInfo') != "")
	<div class="message info">
		<div>{{ Session::get('messageInfo') }}</div>
	</div>
@endif

@if (isset($messages['info']) && $messages['info'] != "")
	<div class="message info">
		<div>{{ $messages['info'] }}</div>

		@if (isset($messages['infoSub']))
			<div class="sub">{{ $messages['infoSub'] }}</div>
		@endif
	</div>
@endif