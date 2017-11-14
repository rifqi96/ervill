@if($errors->any())
	<div class="alert alert-danger alert-dismissable fade in" style="position: fixed; bottom: 0; right: 5px;">
		<p><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
		<ul>
		@foreach($errors->all() as $error)
			<li>{{$error}}</li>
		@endforeach
		</ul>
	</div>
@endif