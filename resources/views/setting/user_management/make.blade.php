@extends('layouts.master')

@section('title')
Tambah User
@endsection

@section('content')
    <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
        <a href="{{route('setting.user_management.index')}}"><button class="btn btn-primary">Lihat Daftar User</button></a> 
    </header>

    <section class="box-typical box-typical-padding">       

        <form action="{{route('setting.user_management.do.make')}}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Role</label>
                <div class="col-sm-10">
                    <p class="form-control-static">
	                    <select id="role" name="role" class="form-control">
	                        <option value=""></option>
                            @foreach($roles as $role)
                                <option value="{{$role->id}}">{{$role->name}}</option>
                            @endforeach
	                    </select>
                    </p>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Username</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><input type="text" class="form-control" name="username" placeholder="Username"></p>                  
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Password</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><input type="password" class="form-control" name="password" placeholder="Password"></p>                  
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Password Confirmation</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><input type="password" class="form-control" name="password_confirmation" placeholder="Password Confirmation"></p>                  
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Nama</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><input type="text" class="form-control" name="full_name" placeholder="Nama"></p>                  
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">E-mail</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><input type="email" class="form-control" name="email" placeholder="E-mail"></p>                  
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Nomor Telepon</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><input type="text" class="form-control" name="phone" placeholder="Nomor Telepon"></p>                  
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
@endsection