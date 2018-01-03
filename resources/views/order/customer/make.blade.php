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
                    <div id="new-customer-input">
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label" for="type">Agen ?</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><input type="checkbox" class="form-control checkbox" name="type" id="type" value="agent"></p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Jenis Pembelian</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <select id="purchase_type" name="purchase_type" class="form-control">
                                        <option value="">--</option>
                                        <option value="rent">Sewa Galon</option>
                                        <option value="purchase">Beli Galon</option>      
                                        <option value="non_ervill">Tukar Galon Non-Ervill</option>
                                    </select>
                                </p>
                            </div>
                        </div>
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
                            <p class="form-control-static"><input id="quantity" type="number" class="form-control" name="quantity" placeholder="Pilih Customer" max="" min="0"></p>
                        </div>
                    </div>
                    <div class="form-group row" id="add_gallon_div_checkbox">
                        <label class="col-sm-2 form-control-label" for="add_gallon">Tambah Galon ?</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><input type="checkbox" class="form-control checkbox" name="add_gallon" id="add_gallon" value="add_gallon"></p>
                        </div>
                    </div>

                    <div id="add_gallon_div">
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Jenis Pembelian</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <select id="add_gallon_purchase_type" name="add_gallon_purchase_type" class="form-control">
                                        <option value="">--</option>
                                        <option value="rent">Sewa Galon</option>
                                        <option value="purchase">Beli Galon</option>      
                                        <option value="non_ervill">Tukar Galon Non-Ervill</option>
                                    </select>
                                </p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Jumlah Gallon Tambah</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><input type="number" class="form-control" name="add_gallon_quantity" id="add_gallon_quantity" placeholder="Jumlah Gallon (Stock Gudang: {{$inventory->quantity}})" max="{{$inventory->quantity}}" min="1"></p>
                            </div>
                        </div>
                    </div>
                    
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

    <script>
        $(document).ready(function () {
            // Init
            $('#new-customer-input').hide();
            $('#add_gallon_div').hide();
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
                $('#quantity').val('');                               
                if(this.checked){
                    $(this).val(1);
                    $('#empty_gallon').val("");
                    $('#empty_gallon').prop("checked", false);                    
                    $('#new-customer-input').fadeIn();
                    $('.customer-table-container').fadeOut();
                    $('#customer-table .customer-id').prop('checked', false);

                    //reset fields
                    $('#quantity').attr('placeholder','Jumlah Gallon (Stock Gudang: {{$inventory->quantity}})');
                    $('#quantity').attr('max', {{$inventory->quantity}});
                    $('#add_gallon_purchase_type').val('');
                    $('#add_gallon_quantity').val('');
                    $('#add_gallon').attr('checked',false);
                    $('#add_gallon_div').fadeOut(); 
                    $('#add_gallon_div_checkbox').fadeOut(); 
                }
                else{
                    $(this).val("");
                    $('#empty_gallon').val("1");
                    $('#empty_gallon').prop("checked", true);
                    $('#new-customer-input').fadeOut();
                    $('.customer-table-container').fadeIn();
                    $('#new-customer-input input').val("");
                    $('#new-customer-input textarea').val("");

                    $('#quantity').attr('placeholder','Pilih Customer');
                    $('#add_gallon_div_checkbox').fadeIn(); 
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

            //get max quantity for specific customer
            $('#customer-table').on('click', '.customer-id', function () {    
                $.ajax({
                    url: '/getCustomerGallon',
                    type: 'GET',
                    data: {customer_id: $(this).val()},
                    success: function(result){
                        $('#quantity').val('');
                        $('#quantity').attr('placeholder','Jumlah Gallon (Galon Customer: '+result+')');
                        $('#quantity').attr('max', result);

                        //reset add_gallon fields
                        $('#add_gallon_quantity').val('');
                        $('#add_gallon_quantity').attr('placeholder','Jumlah Gallon (Stock Gudang: {{$inventory->quantity}})');
                        $('#add_gallon_quantity').attr('max', {{$inventory->quantity}});
                    }
                });      
                
            });

            $('#quantity').on('change', function () {
                var max_add_gallon_quantity = {{$inventory->quantity}} - $(this).val();
                $('#add_gallon_quantity').attr('placeholder','Jumlah Gallon (Stock Gudang: '+max_add_gallon_quantity+')');
                $('#add_gallon_quantity').attr('max', max_add_gallon_quantity);
            });

            //add more gallon
            $('#add_gallon').on('change', function () {
                $('#add_gallon_purchase_type').val('');
                $('#add_gallon_quantity').val('');
                $('#add_gallon_quantity').attr('placeholder','Jumlah Gallon (Stock Gudang: {{$inventory->quantity}})');
                $('#add_gallon_quantity').attr('max', {{$inventory->quantity}});
                if(this.checked){
                    $('#add_gallon_div').fadeIn();                    
                }
                else{
                    $('#add_gallon_div').fadeOut(); 
                }
            });
        });
    </script>

@endsection