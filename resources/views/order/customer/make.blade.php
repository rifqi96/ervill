@extends('layouts.master')

@section('title')
Pesan Customer
@endsection

@section('content')

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <!--<h3 class="panel-title">Pesan Customer</h3>-->
                <a href="{{route('order.customer.index')}}"><button class="btn btn-primary">Lihat Pesanan Customer</button></a>               
            </header>

            <section class="box-typical box-typical-padding">
                {{--<h5 class="m-t-lg with-border">Input New Task Data</h5>--}}
                {{--<check if="{{@SESSION.addtasksuccess}}">--}}
                    {{--<h4><span class="label label-success">{{@SESSION.addtasksuccess}}</span></h4>--}}
                {{--</check>--}}

                <form action="{{route('order.customer.do.make')}}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Customer</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><input type="text" class="form-control" name="customer_name" placeholder="Nama Customer"></p>
                            {{--<check if="{{@SESSION.addtaskerror['name']}}">--}}
                                {{--<small class="text-muted"><repeat group="{{@SESSION.addtaskerror['name']}}" value="{{@text}}">{{@text}} ;</repeat></small>--}}
                            {{--</check>--}}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Alamat Customer</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><textarea class="form-control" name="customer_address" placeholder="Alamat Customer" rows="5"></textarea></p>
                            {{--<check if="{{@SESSION.addtaskerror['company_name']}}">--}}
                                {{--<small class="text-muted"><repeat group="{{@SESSION.addtaskerror['company_name']}}" value="{{@text}}">{{@text}} ;</repeat></small>--}}
                            {{--</check>--}}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Jumlah Gallon</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><input type="number" class="form-control" name="quantity" placeholder="Jumlah Gallon"></p>
                            {{--<check if="{{@SESSION.addtaskerror['company_name']}}">--}}
                                {{--<small class="text-muted"><repeat group="{{@SESSION.addtaskerror['company_name']}}" value="{{@text}}">{{@text}} ;</repeat></small>--}}
                            {{--</check>--}}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Tgl Pengiriman</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                                <input type="date" class="form-control" name="delivery_at" placeholder="Tgl Pengiriman" value="{{\Carbon\Carbon::now()->toDateString()}}">
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




            
        </div>
    </div>

@endsection