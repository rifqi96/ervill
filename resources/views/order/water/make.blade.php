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
                {{--<h5 class="m-t-lg with-border">Input New Task Data</h5>--}}
                {{--<check if="{{@SESSION.addtasksuccess}}">--}}
                    {{--<h4><span class="label label-success">{{@SESSION.addtasksuccess}}</span></h4>--}}
                {{--</check>--}}

                <form action="" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Outsourcing Pabrik</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">                 
                                <select id="outsourcing_factory" name="outsourcing_factory" class="form-control">
                                    <option value=""></option>
                                    <option value="1">Outsourcing Pabrik 1</option>
                                    <option value="2">Outsourcing Pabrik 2</option>
                                    <option value="3">Outsourcing Pabrik 3</option>
                                </select>
                            </p>
                        </div>
                    </div>      

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
                            <p class="form-control-static"><input type="number" class="form-control" name="quantity" placeholder="Max Gallon: 100"></p>
                            {{--<check if="{{@SESSION.addtaskerror['company_name']}}">--}}
                                {{--<small class="text-muted"><repeat group="{{@SESSION.addtaskerror['company_name']}}" value="{{@text}}">{{@text}} ;</repeat></small>--}}
                            {{--</check>--}}
                        </div>
                    </div> 
                    <!--<div class="form-group row">
                        <label class="col-sm-2 form-control-label">Jumlah Retur Galon</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><input type="number" class="form-control" name="quantity" placeholder="Max Galon yang Rusak: 5"></p>
                            {{--<check if="{{@SESSION.addtaskerror['company_name']}}">--}}
                                {{--<small class="text-muted"><repeat group="{{@SESSION.addtaskerror['company_name']}}" value="{{@text}}">{{@text}} ;</repeat></small>--}}
                            {{--</check>--}}
                        </div>
                    </div>-->                   
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
            
        </div>
    </div>

@endsection