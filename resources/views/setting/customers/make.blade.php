@extends('layouts.master')

@section('title')
Tambah Customer Baru
@endsection

@section('content')
    <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
        <a href="{{route('setting.customers.index')}}"><button class="btn btn-primary">Lihat Daftar Customer</button></a>
    </header>

    <section class="box-typical box-typical-padding">       

        <form action="{{route('setting.customers.do.make')}}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Nama</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><input type="text" class="form-control" name="name" placeholder="Nama"></p>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Nomor Telepon</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><input type="text" class="form-control" name="phone" placeholder="Nomor Telepon"></p>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Alamat</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><textarea name="address" id="address" cols="30" rows="10" class="form-control" placeholder="Alamat"></textarea></p>
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