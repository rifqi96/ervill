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
                {{--<h5 class="m-t-lg with-border">Input New Task Data</h5>--}}
                {{--<check if="{{@SESSION.addtasksuccess}}">--}}
                    {{--<h4><span class="label label-success">{{@SESSION.addtasksuccess}}</span></h4>--}}
                {{--</check>--}}

                <form action="" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Deskripsi Masalah</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><textarea class="form-control" name="description" placeholder="Deskripsi Masalah" rows="5"></textarea></p>
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