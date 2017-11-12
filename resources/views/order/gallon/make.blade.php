@extends('layouts.master')

@section('title')
Pesan Gallon
@endsection

@section('content')
    <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
        {{--<h3 class="panel-title"></h3>--}}
        <a href="{{route('order.gallon.index')}}"><button class="btn btn-primary">Lihat Pesanan Gallon</button></a>
        <a href="{{route('order.gallon.inventory')}}"><button class="btn btn-primary">Stock Gudang</button></a>
    </header>

    <section class="box-typical box-typical-padding">
        {{--<h5 class="m-t-lg with-border">Input New Task Data</h5>--}}
        {{--<check if="{{@SESSION.addtasksuccess}}">--}}
            {{--<h4><span class="label label-success">{{@SESSION.addtasksuccess}}</span></h4>--}}
        {{--</check>--}}

        <form action="{{route('order.gallon.do.make')}}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Outsourcing Pengemudi</label>
                <div class="col-sm-10">
                    <p class="form-control-static">                 
                        <select id="outsourcing_driver" name="outsourcing_driver" class="form-control">
                            <option value=""></option>
                            <option value="1">Outsourcing Pengemudi 1</option>
                            <option value="2">Outsourcing Pengemudi 2</option>
                            <option value="3">Outsourcing Pengemudi 3</option>
                        </select> 
                    </p>
                </div>
            </div>    
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Jumlah Gallon</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><input type="number" class="form-control" name="qty" placeholder="Jumlah Gallon"></p>
                    {{--<check if="{{@SESSION.addtaskerror['company_name']}}">--}}
                        {{--<small class="text-muted"><repeat group="{{@SESSION.addtaskerror['company_name']}}" value="{{@text}}">{{@text}} ;</repeat></small>--}}
                    {{--</check>--}}
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Tgl Pengiriman</label>
                <div class="col-sm-10">
                    <p class="form-control-static">
                        <input type="date" class="form-control" name="delivery_at" placeholder="Tgl Pengiriman">
                    </p>
                    {{--<check if="{{@SESSION.addtaskerror['status']}}">--}}
                        {{--<small class="text-muted"><repeat group="{{@SESSION.addtaskerror['status']}}" value="{{@text}}">{{@text}} ;</repeat></small>--}}
                    {{--</check>--}}
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