@extends('layouts.master')

@section('title')
Profile
@endsection

@section('content')
    <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
        
    </header>

    <section class="box-typical box-typical-padding">       

        <form action="{{route('profile.do.update')}}" method="POST" enctype="multipart/form-data">
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
                <div class="col-sm-2"></div>
                <div class="col-sm-10">
                    <input type="submit" value="Submit" class="btn">
                    <input type="reset" value="Reset" class="btn btn-info">
                </div>
            </div>
        </form>
    </section><!--.box-typical-->
@endsection