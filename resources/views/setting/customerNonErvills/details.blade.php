@extends('layouts.master')

@section('title')
    Detail Customer Pihak Ketiga
@endsection

@section('content')
    <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
        <a href="{{route('setting.customerNonErvills.index')}}">
            <button class="btn btn-primary">Lihat Daftar Customer Pihak Ketiga</button>
        </a>
    </header>

    <section class="box-typical box-typical-padding">

        <div class="form-group row">
            <label class="col-sm-2 form-control-label">No. Customer</label>
            <div class="col-sm-10">
                <p class="form-control-static"><input type="text" class="form-control" name="id" placeholder="No. Customer" value="{{$customer->id}}" readonly=""></p>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">Nama</label>
            <div class="col-sm-10">
                <p class="form-control-static"><input type="text" class="form-control" name="name" placeholder="Nama Customer" value="{{$customer->name}}" readonly=""></p>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">Alamat</label>
            <div class="col-sm-10">
                <p class="form-control-static">
                    <textarea name="address" id="address" cols="30" rows="10" readonly="" class="form-control">{{$customer->address}}</textarea>
                </p>
            </div>
        </div>        

        <div class="form-group row">
            <label class="col-sm-2 form-control-label">Galon Aqua</label>
            <div class="col-sm-2">
                <p class="form-control-static">
                    <input type="text" class="form-control" name="type" placeholder="Type" value="{{$customer->aqua_qty ? $customer->aqua_qty : '-'}}" id="rent" readonly="">
                </p>
            </div>
            <label class="col-sm-2 form-control-label">Galon Non Aqua</label>
            <div class="col-sm-2">
                <p class="form-control-static">
                    <input type="text" class="form-control" name="type" placeholder="Type" value="{{$customer->non_aqua_qty ? $customer->non_aqua_qty : '-'}}" id="purchase" readonly="">
                </p>
            </div>            
        </div>        
        
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">Tgl Pembuatan</label>
            <div class="col-sm-10">
                <p class="form-control-static"><input type="text" class="form-control" name="type" placeholder="Type" value="{{$customer->created_at}}" id="created_at" readonly=""></p>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">Tgl Update</label>
            <div class="col-sm-10">
                <p class="form-control-static"><input type="text" class="form-control" name="type" placeholder="Type" value="{{$customer->updated_at}}" id="updated_at" readonly=""></p>
            </div>
        </div>
    </section><!--.box-typical-->

    <script type="text/javascript">
        $(document).ready(function(){
            
            //var last_transaction = $('#lasttransaction').val();
            //var overdue = $('#overdue').val();
            //var overdue_date = $('#overduedate').val();
            //var type = $('#type').val();
            var created_at = $('#created_at').val();
            var updated_at = $('#updated_at').val();

            $('#created_at').val(moment(created_at).locale('id').format('DD/MM/YYYY HH:mm:ss'));
            $('#updated_at').val(moment(updated_at).locale('id').format('DD/MM/YYYY HH:mm:ss'));

            // if(!notif_day){
            //     $('#notifday').val('14 Hari dari pengiriman terakhir');
            // }

            //$('#lasttransaction').val(moment(last_transaction).locale('id').format('DD/MM/YYYY'));
            //$('#overduedate').val(moment(overdue_date).locale('id').format('DD/MM/YYYY'));

            // if(overdue < 0){
            //     $('#overdue').val('Lewat ' + Math.abs(overdue) + ' hari');
            // }
            // else if(overdue === 0){
            //     $('#overdue').val('Hari ini');
            // }
            // else{
            //     $('#overdue').val('Masih ' + Math.abs(overdue) + ' hari');
            // }

            // if(type == 'end_customer'){
            //     $('#type').val('End Customer');
            // }
            // else{
            //     $('#type').val('Agen');
            // }
        });
    </script>
@endsection