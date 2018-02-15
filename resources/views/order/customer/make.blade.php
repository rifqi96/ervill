@extends('layouts.master')

@section('title')
Pesan Customer
@endsection

@section('content')

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <a href="{{route('order.customer.index')}}"><button class="btn btn-primary">Lihat Pesanan Customer</button></a>               
            </header>

            <section class="box-typical box-typical-padding">

                <form action="{{route('order.customer.do.make')}}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label" for="new-customer">Customer Baru ?</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                                <input type="checkbox" class="form-control checkbox new-customer" id="new-customer" name="new_customer" value="0">
                            </p>
                        </div>
                    </div>
                    <div class="form-group row customer-table-container">
                        <div class="col-sm-12">
                            <h4 class="box-typical-header"><label for="existingCustomerTable" class="form-control-label">Silahkan pilih customer</label></h4>
                            <table id="customer-table">
                                <thead>
                                    <th></th>
                                    <th>ID</th>
                                    <th>Nama Customer</th>
                                    <th>No. Telepon</th>
                                    <th>Alamat</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="form-group row" id="empty_gallon_div">
                        <label class="col-sm-2 form-control-label" for="empty_gallon">Tukar Galon ?</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><input type="checkbox" class="form-control checkbox" name="empty_gallon" id="empty_gallon" value=""></p>
                        </div>
                    </div>
                    <div id="new-customer-input">
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Nama Customer</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><input type="text" class="form-control" name="name" placeholder="Nama Customer"></p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">No. Telepon</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><input type="text" class="form-control" name="phone" placeholder="No. Telepon"></p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Alamat Customer</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><textarea class="form-control" name="address" placeholder="Alamat Customer" rows="5"></textarea></p>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Jumlah Gallon</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><input type="number" class="form-control" name="quantity" placeholder="Jumlah Gallon (Stock Gudang: {{$inventory->quantity}})" max="{{$inventory->quantity}}" min="1"></p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Tgl Pengiriman</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                                <input type="date" class="form-control" name="delivery_at" placeholder="Tgl Pengiriman" value="{{\Carbon\Carbon::now()->toDateString()}}" min="{{\Carbon\Carbon::now()->toDateString()}}">
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

    <script>
        $(document).ready(function () {
            // Init
            $('#new-customer-input').hide();
            $('#empty_gallon').prop('checked', true);
            $('#empty_gallon').val("1");

            $('#customer-table').dataTable({
                scrollX: true,
                fixedHeader: true,
                ajax: {
                    url: '/getCustomers',
                    dataSrc: ''
                },
                columns: [
                    {data: null,
                    render: function (data, type, row, meta) {
                        return '<input class="radio customer-id" type="radio" name="customer_id" value="'+data.id+'">';
                    }},
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'address'},
                    {data: 'phone'}
                    ],
                processing: true,
                'order':[1, 'desc']
            });
            
            $('#new-customer').on('change', function () {
                if(this.checked){
                    $(this).val(1);
                    $('#empty_gallon').val("");
                    $('#empty_gallon').prop("checked", false);
                    $('#new-customer-input').fadeIn();
                    $('.customer-table-container').fadeOut();
                    $('#customer-table .customer-id').prop('checked', false);
                    $('#empty_gallon_div').fadeOut();
                }
                else{
                    $(this).val("");
                    $('#empty_gallon').val("1");
                    $('#empty_gallon').prop("checked", true);
                    $('#new-customer-input').fadeOut();
                    $('.customer-table-container').fadeIn();
                    $('#new-customer-input input').val("");
                    $('#new-customer-input textarea').val("");
                    $('#empty_gallon_div').fadeIn();
                }
            });

            $('#empty_gallon').on('change', function(){
                if(this.checked){
                    $(this).val("1");
                }
                else{
                    $(this).val("");
                }
            });
        });
    </script>

@endsection