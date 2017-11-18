@extends('layouts.master')

@section('title')
Buat Outsourcing
@endsection

@section('content')
    <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
        {{--<h3 class="panel-title"></h3>--}}
        <a href="{{route('setting.outsourcing.index')}}"><button class="btn btn-primary">Lihat Daftar Outsourcing</button></a> 
    </header>

    <section class="box-typical box-typical-padding">

        <form action="{{route('setting.outsourcing.do.make')}}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Jenis</label>
                <div class="col-sm-10">
                    <p class="form-control-static">
	                    <select id="type" name="type" class="form-control">
	                        <option value=""></option>
	                        <option value="1">Pengemudi</option>
	                        <option value="2">Pabrik Air</option>	                       
	                    </select>
                    </p>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Nama</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><input type="text" class="form-control" name="name" placeholder="Nama Outsourcing"></p>                  
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