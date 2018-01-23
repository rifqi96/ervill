@extends('layouts.master')

@section('title')
Pesan Air
@endsection

@section('content')

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <!--<h3 class="panel-title">Pesan Customer</h3>-->
                <a href="{{route('order.water.index')}}"><button class="btn btn-primary">Lihat Pesanan Air</button></a>               
            </header>

            <section class="box-typical box-typical-padding">
                <form action="{{route('order.water.do.make')}}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    {{--<div class="form-group row">--}}
                        {{--<label class="col-sm-2 form-control-label">Outsourcing Pabrik Air</label>--}}
                        {{--<div class="col-sm-10">--}}
                            {{--<p class="form-control-static">                 --}}
                                {{--<select id="outsourcing_water" name="outsourcing_water" class="form-control">--}}
                                    {{--<option value=""></option>--}}
                                    {{--@foreach($outsourcingWaters as $outsourcingWater)--}}
                                        {{--<option value="{{$outsourcingWater->id}}">{{$outsourcingWater->name}}</option>           --}}
                                    {{--@endforeach--}}
                                {{--</select>--}}
                            {{--</p>--}}
                        {{--</div>--}}
                    {{--</div>      --}}

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Outsourcing Pengemudi</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">                 
                                <select id="outsourcing_driver" name="outsourcing_driver" class="form-control">
                                    <option value=""></option>
                                    @foreach($outsourcingDrivers as $outsourcingDriver)
                                        <option value="{{$outsourcingDriver->id}}">{{$outsourcingDriver->name}}</option>           
                                    @endforeach
                                </select> 
                            </p>
                        </div>
                    </div>            
                    
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Jumlah Galon Buffer</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><input type="number" class="form-control" name="buffer_qty" placeholder="Maks Galon Buffer: {{$max_buffer_qty}}" min="0" max="{{$max_buffer_qty}}"></p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Jumlah Galon Gudang</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><input type="number" class="form-control" name="warehouse_qty" placeholder="Maks Galon Gudang: {{$max_warehouse_qty}}" min="0" max="{{$max_warehouse_qty}}"></p>
                        </div>
                    </div>
                    <!--<div class="form-group row">
                        <label class="col-sm-2 form-control-label">Jumlah Retur Galon</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><input type="number" class="form-control" name="quantity" placeholder="Max Galon yang Rusak: 5"></p>
                        </div>
                    </div>-->                   
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Tgl Pengiriman</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                                <input type="date" class="form-control" name="delivery_at" placeholder="Tgl Pengiriman" value="{{\Carbon\Carbon::now()->toDateString()}}">
                            </p>
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