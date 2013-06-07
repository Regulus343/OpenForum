{{-- Success Message --}}
@if (!is_null(Session::get('messageSuccess')) && Session::get('messageSuccess') != "")
	<div class="message success">
		<div>{{ Session::get('messageSuccess') }}</div>

		@if (!is_null(Session::get('messageSuccessSub')) && Session::get('messageSuccessSub') != "")
			<div class="sub">{{ Session::get('messageSuccessSub') }}</div>
		@endif
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

{{-- Error Message --}}
@if (!is_null(Session::get('messageError')) && Session::get('messageError') != "")
	<div class="message error">
		<div>{{ Session::get('messageError') }}</div>

		@if (!is_null(Session::get('messageErrorSub')) && Session::get('messageErrorSub') != "")
			<div class="sub">{{ Session::get('messageErrorSub') }}</div>
		@endif
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

{{-- General Info Message --}}
@if (!is_null(Session::get('messageInfo')) && Session::get('messageInfo') != "")
	<div class="message info">
		<div>{{ Session::get('messageInfo') }}</div>

		@if (!is_null(Session::get('messageInfoSub')) && Session::get('messageInfoSub') != "")
			<div class="sub">{{ Session::get('messageInfoSub') }}</div>
		@endif
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

{{-- JS Messages --}}
<div id="message-forum-thread">

	{{-- Success Message --}}
	<div class="message success hidden">
		<div class="main"></div>
		<div class="sub"></div>
	</div>

	{{-- Error Message --}}
	<div class="message error hidden">
		<div class="main"></div>
		<div class="sub"></div>
	</div>

	{{-- General Info Message --}}
	<div class="message info hidden">
		<div class="main"></div>
		<div class="sub"></div>
	</div>

</div><!-- /#message-forum-thread -->