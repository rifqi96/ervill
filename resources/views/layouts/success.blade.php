@if(session()->has('success'))
    <div class="alert alert-success alert-dismissable fade in" style="position: fixed; bottom: 0; right: 5px;">
        <p><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
        {{ session()->get('success') }}
    </div>
@endif