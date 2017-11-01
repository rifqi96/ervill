@extends('layouts.master')

@section('title')
Order Water Issue
@endsection

@section('content')

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <!--<h3 class="panel-title">Isu Air</h3>-->
                <a href="{{route('order.water.index')}}"><button class="btn btn-primary">Lihat Pesanan Air</button></a>   
            </header>

            <section class="box-typical box-typical-padding">              

                <form action="" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Nama Pengemudi</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><input type="text" class="form-control" name="driver_name" placeholder="Nama Pengemudi"></p>
                           
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Deskripsi Masalah</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><textarea class="form-control" name="description" placeholder="Deskripsi Masalah" rows="5"></textarea></p>
                           
                        </div>
                    </div>               
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Jumlah Galon yang Beramsalah</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><input type="number" class="form-control" name="quantity" placeholder="Jumlah Gallon"></p>
                           
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
            
        </div>
    </div>

@endsection