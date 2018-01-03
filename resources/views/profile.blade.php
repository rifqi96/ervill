@extends('layouts.master')

@section('title')
Profile
@endsection

@section('content')
    <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
        
    </header>

    <section class="box-typical box-typical-padding">       

        <form action="{{route('profile.do.updateProfile')}}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}       
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Role</label>
                <div class="col-sm-10">
                    <p class="form-control-static">
	                    {{auth()->user()->role->name}}
                    </p>
                </div>
            </div>     
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Username</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><input type="text" class="form-control" name="username" placeholder="Username" value="{{auth()->user()->username}}"></p>                  
                </div>
            </div>                     
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Nama</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><input type="text" class="form-control" name="full_name" placeholder="Nama" value="{{auth()->user()->full_name}}"></p>                  
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">E-mail</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><input type="email" class="form-control" name="email" placeholder="E-mail" value="{{auth()->user()->email}}"></p>                  
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Nomor Telepon</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><input type="text" class="form-control" name="phone" placeholder="Nomor Telepon" value="{{auth()->user()->phone}}"></p>                  
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Ganti Password ?</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><input type="checkbox" class="form-control" name="change_password" id="change_password"></p>                  
                </div>
            </div> 
            <div id="change_password_div">
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Password Baru</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><input type="password" class="form-control" name="password" placeholder="Password Baru"></p>                  
                    </div>
                </div>   
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Konfirmasi Password Baru</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><input type="password" class="form-control" name="password_confirmation" placeholder="Konfirmasi Password Baru"></p>                  
                    </div>
                </div>   
            </div>

             
            
            <div class="form-group row">
                <div class="col-sm-2"></div>
                <div class="col-sm-10">
                    <input type="submit" value="Submit" class="btn">
                    <input type="reset" value="Reset" class="btn btn-info">
                </div>
            </div>
        </form>
    </section><!--.box-typical-->

    <script type="text/javascript">
        $(document).ready(function(){
            $('#change_password_div').hide();
            $("#change_password").change(function() {
                $('#change_password_div input').val('');
                if(this.checked)
                    $('#change_password_div').fadeIn();
                else
                    $('#change_password_div').fadeOut();                
            });
        });
    </script>
@endsection