@extends('layouts.master')

@section('title')
    Detail User
@endsection

@section('content')
    <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
        <a href="{{route('setting.user_management.index')}}">
            <button class="btn btn-primary">Lihat list user</button>
        </a>
    </header>

    <section class="box-typical box-typical-padding">

        <div class="form-group row">
            <label class="col-sm-2 form-control-label">Role</label>
            <div class="col-sm-10">
                <p class="form-control-static">
                    {{$user->role->name}}
                </p>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">User ID</label>
            <div class="col-sm-10">
                <p class="form-control-static"><input type="text" class="form-control" name="id" placeholder="Username" value="{{$user->id}}" readonly=""></p>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">Username</label>
            <div class="col-sm-10">
                <p class="form-control-static"><input type="text" class="form-control" name="username" placeholder="Username" value="{{$user->username}}" readonly=""></p>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">Nama</label>
            <div class="col-sm-10">
                <p class="form-control-static"><input type="text" class="form-control" name="full_name" placeholder="Nama" value="{{$user->full_name}}" readonly=""></p>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">E-mail</label>
            <div class="col-sm-10">
                <p class="form-control-static"><input type="email" class="form-control" name="email" placeholder="E-mail" value="{{$user->email}}" readonly=""></p>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">Nomor Telepon</label>
            <div class="col-sm-10">
                <p class="form-control-static"><input type="text" class="form-control" name="phone" placeholder="Nomor Telepon" value="{{$user->phone}}" readonly=""></p>
            </div>
        </div>
    </section><!--.box-typical-->

    <script type="text/javascript">
        $(document).ready(function(){
        });
    </script>
@endsection