@extends('layouts.master')

@section('title')
    Detail Customer
@endsection

@section('content')
    <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
        <a href="{{route('setting.customers.index')}}">
            <button class="btn btn-primary">Lihat Daftar Customer</button>
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
            <label class="col-sm-2 form-control-label">Jenis Customer</label>
            <div class="col-sm-10">
                <p class="form-control-static"><input type="text" class="form-control" name="type" placeholder="Type" value="{{$customer->type}}" id="type" readonly=""></p>
            </div>
        </div>
        @if($customer->customerGallons->count() > 0 && $customer->order_customers->count() > 0)
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">Galon Pinjam</label>
            <div class="col-sm-2">
                <p class="form-control-static">
                    @if($customer->customerGallons->count()>0)
                        @foreach($customer->customerGallons as $key => $val)
                            @if($val->type == 'rent')
                                <input type="text" class="form-control" name="type" placeholder="Type" value="{{$val->qty}}" id="rent" readonly="">
                                @php
                                    break;
                                @endphp
                            @endif

                            @if($customer->customerGallons->count()-1 == $key && $val->type != 'rent')
                                <input type="text" class="form-control" name="type" placeholder="Type" value="0" readonly="">
                            @endif
                        @endforeach
                    @else
                        <input type="text" class="form-control" name="type" placeholder="Type" value="0" readonly="">
                    @endif
                </p>
            </div>
            <label class="col-sm-2 form-control-label">Galon Beli</label>
            <div class="col-sm-2">
                <p class="form-control-static">
                    @if($customer->customerGallons->count()>0)
                        @foreach($customer->customerGallons as $key => $val)
                            @if($val->type == 'purchase')
                                <input type="text" class="form-control" name="type" placeholder="Type" value="{{$val->qty}}" id="purchase" readonly="">
                                @php
                                    break;
                                @endphp
                            @endif

                            @if($customer->customerGallons->count()-1 == $key && $val->type != 'purchase')
                                <input type="text" class="form-control" name="type" placeholder="Type" value="0" readonly="">
                            @endif
                        @endforeach
                    @else
                        <input type="text" class="form-control" name="type" placeholder="Type" value="0" readonly="">
                    @endif
                </p>
            </div>
            <label class="col-sm-2 form-control-label">Galon Tukar Non Ervill</label>
            <div class="col-sm-2">
                <p class="form-control-static">
                    @if($customer->customerGallons->count()>0)
                        @foreach($customer->customerGallons as $key => $val)
                            @if($val->type == 'non_ervill')
                                <input type="text" class="form-control" name="type" placeholder="Type" value="{{$val->qty}}" id="non_ervill" readonly="">
                                @php
                                    break;
                                @endphp
                            @endif

                            @if($customer->customerGallons->count()-1 == $key && $val->type != 'non_ervill')
                                <input type="text" class="form-control" name="type" placeholder="Type" value="0" readonly="">
                            @endif
                        @endforeach
                    @else
                        <input type="text" class="form-control" name="type" placeholder="Type" value="0" readonly="">
                    @endif
                </p>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">Diperingatkan Setiap</label>
            <div class="col-sm-10">
                <p class="form-control-static"><input type="text" class="form-control" name="type" placeholder="Type" value="{{$customer->notif_day}} hari dari pengiriman terakhir" id="notifday" readonly=""></p>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">Pengiriman Terakhir</label>
            <div class="col-sm-10">
                <p class="form-control-static"><input type="text" class="form-control" name="type" placeholder="Type" value="{{$customer->last_transaction}}" id="lasttransaction" readonly=""></p>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">Tgl Overdue</label>
            <div class="col-sm-10">
                <p class="form-control-static"><input type="text" class="form-control" name="type" placeholder="Type" value="{{$customer->overdue_date}}" id="overduedate" readonly=""></p>
            </div>
        </div>
        <div class="form-group row">
            <label class="col-sm-2 form-control-label">Hari Overdue</label>
            <div class="col-sm-10">
                <p class="form-control-static"><input type="text" class="form-control" name="type" placeholder="Type" value="{{$customer->overdue}}" id="overdue" readonly=""></p>
            </div>
        </div>
        @endif
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
            var notif_day = "{{$customer->notif_day}}";
            var last_transaction = $('#lasttransaction').val();
            var overdue = $('#overdue').val();
            var overdue_date = $('#overduedate').val();
            var type = $('#type').val();
            var created_at = $('#created_at').val();
            var updated_at = $('#updated_at').val();

            $('#created_at').val(moment(created_at).locale('id').format('DD MMMM YYYY HH:mm:ss'));
            $('#updated_at').val(moment(updated_at).locale('id').format('DD MMMM YYYY HH:mm:ss'));

            if(!notif_day){
                $('#notifday').val('14 Hari dari pengiriman terakhir');
            }

            $('#lasttransaction').val(moment(last_transaction).locale('id').format('DD MMMM YYYY'));
            $('#overduedate').val(moment(overdue_date).locale('id').format('DD MMMM YYYY'));

            if(overdue < 0){
                $('#overdue').val('Lewat ' + Math.abs(overdue) + ' hari');
            }
            else if(overdue === 0){
                $('#overdue').val('Hari ini');
            }
            else{
                $('#overdue').val('Masih ' + Math.abs(overdue) + ' hari');
            }

            if(type == 'end_customer'){
                $('#type').val('End Customer');
            }
            else{
                $('#type').val('Agen');
            }
        });
    </script>
@endsection