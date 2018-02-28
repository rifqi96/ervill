@extends('layouts.master')

@section('title')
Pesan Gallon
@endsection

@section('content')
    <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
        <a href="{{route('order.gallon.index')}}"><button class="btn btn-primary">Lihat Pesanan Gallon</button></a>
    </header>

    <section class="box-typical box-typical-padding">
        <form action="{{route('order.gallon.do.make')}}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="form-group row">
                <label class="col-sm-2 form-control-label">No Surat Pembelian</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><input type="text" class="form-control" name="purchase_invoice_no" placeholder="No Surat Pembelian"></p>                    
                </div>
            </div>
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
                <label class="col-sm-2 form-control-label">Jumlah Gallon</label>
                <div class="col-sm-10">
                    <p class="form-control-static"><input type="number" class="form-control" name="quantity" placeholder="Jumlah Gallon"></p>                    
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-2 form-control-label">Tgl Pembelian</label>
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
@endsection