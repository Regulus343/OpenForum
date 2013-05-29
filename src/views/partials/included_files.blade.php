{{-- Load jQuery --}}
@if (Config::get('open-forum::loadJquery'))

	<script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>

@endif

{{-- Load Bootstrap CSS & JS --}}
@if (Config::get('open-forum::loadBootstrap'))

	<link type="text/css" rel="stylesheet" href="{{ Site::css('bootstrap', 'regulus/open-forum') }}" />
	<script type="text/javascript" src="{{ Site::js('bootstrap.min', 'regulus/open-forum') }}"></script>

@endif

{{-- Load Boxy --}}
@if (Config::get('open-forum::loadBoxy'))

	<link type="text/css" rel="stylesheet" href="{{ Site::css('boxy', 'regulus/open-forum') }}" />
	<script type="text/javascript" src="{{ Site::js('jquery.boxy', 'regulus/open-forum') }}"></script>

@endif

{{-- Forum CSS --}}
<link type="text/css" rel="stylesheet" href="{{ Site::css('forum', 'regulus/open-forum') }}" />

{{-- Forum JS --}}
<script type="text/javascript">
	if (baseURL == undefined) var baseURL = "{{ URL::to('') }}";

	var forumLabels   = {{ json_encode(Lang::get('open-forum::labels')) }};
	var forumMessages = {{ json_encode(Lang::get('open-forum::messages')) }};

	@if (!is_null(Site::get('forumSectionSlug')) && !is_null(Site::get('forumSectionSlug')))
		var forumSectionSlug = "{{ Site::get('forumSectionSlug') }}";
	@else
		if (forumSectionSlug == undefined) var forumSectionSlug = "";
	@endif

	@if (!is_null(Site::get('forumThreadID')) && !is_null(Site::get('forumThreadID')))
		var forumThreadID = "{{ Site::get('forumThreadID') }}";
	@else
		if (forumThreadID == undefined) var forumThreadID   = 0;
	@endif

	var postContent = '{{ !is_null(Input::get('content')) ? Input::get('content') : '' }}';
</script>

<script type="text/javascript" src="{{ Site::js('wysihtml5', 'regulus/open-forum') }}"></script>
<script type="text/javascript" src="{{ Site::js('wysihtml5-parser-rules', 'regulus/open-forum') }}"></script>

<script type="text/javascript" src="{{ Site::js('forum', 'regulus/open-forum') }}"></script>