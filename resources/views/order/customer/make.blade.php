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
                                    <th>No</th>
                                    <th>Nama Customer</th>
                                    <th>Alamat</th>
                                    <th>No. Telepon</th>
                                    <th>Jenis</th>
                                    <th>Aksi</th>
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
                    <div class="input-forms">
                        <div class="form-group row">
                            <div class="col-lg-12">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th rowspan="2" class="refill" style="text-align:center;">Isi ulang air</th>
                                        <th colspan="3" class="pay-gallon" style="text-align:center;">Galon Tambah</th>
                                        <th rowspan="2" class="pay-gallon" style="text-align:center;">Pembayaran Galon</th>
                                    </tr>
                                    <tr>
                                        <th>Pinjam Galon</th>
                                        <th>Beli Galon</th>
                                        <th>Tukar Galon Non Ervill</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <td class="refill">
                                        <p class="form-control-static"><input class="quantity" id="refill-qty" type="number" class="form-control" name="refill_qty" placeholder="Jumlah" max="" min=""></p>
                                    </td>
                                    <td>
                                        <p class="form-control-static"><input class="quantity" type="number" class="form-control" name="rent_qty" placeholder="Jumlah (Maks: {{$inventory->quantity}})" max="{{$inventory->quantity}}" min=""></p>
                                    </td>
                                    <td>
                                        <p class="form-control-static"><input class="quantity" type="number" class="form-control" name="purchase_qty" placeholder="Jumlah (Maks: {{$inventory->quantity}})" max="{{$inventory->quantity}}" min=""></p>
                                    </td>
                                    <td>
                                        <p class="form-control-static"><input class="quantity" type="number" class="form-control" name="non_erv_qty" placeholder="Jumlah (Maks: {{$inventory->quantity}})" max="{{$inventory->quantity}}" min=""></p>
                                    </td>
                                    <td class="pay-gallon">
                                        <p class="form-control-static"><input class="quantity" id="pay-qty" type="number" class="form-control" name="pay_qty" placeholder="" max="" min=""></p>
                                    </td>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Harga Satuan Tambahan</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <input type="number" class="form-control" name="additional_price" placeholder="Jika ada, tidak wajib diisi. Contoh: -2000 (Artinya harga satuan berkurang Rp 2.000,- dan sebaliknya)">
                                </p>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div id="is_piutang_div">
                                <label class="col-sm-2 form-control-label" for="is_piutang">Dibayar dengan Piutang ?</label>
                                <div class="col-sm-2">
                                    <p class="form-control-static"><input type="checkbox" class="form-control checkbox" name="is_piutang" id="is_piutang" value="is_piutang"></p>
                                </div>
                            </div>
                            <div id="is_free_div">
                                <label class="col-sm-2 form-control-label" for="is_free">Gratis/Sample ?</label>
                                <div class="col-sm-2">
                                    <p class="form-control-static"><input type="checkbox" class="form-control checkbox" name="is_free" id="is_free" value="is_free"></p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Tgl Pengiriman</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <input id="delivery_at" type="date" class="form-control" name="delivery_at" placeholder="Tgl Pengiriman" value="{{\Carbon\Carbon::now()->toDateString()}}">
                                </p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Keterangan Tambahan</label>
                            <div class="col-sm-10">
                                <p class="form-control-static">
                                    <textarea name="description" class="form-control" id="" cols="30" rows="10" placeholder="Keterangan / Pesan / Catatan tambahan. Boleh dikosongkan, tidak wajib diisi."></textarea>
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
                    </div>
                </form>
            </section><!--.box-typical-->
        </div>
    </div>

    <!-- Asset Modal -->

    <div class="modal fade" id="assetModal" tabindex="-1" role="dialog" aria-labelledby="assetModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">


                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="assetModalLabel">Aset Customer</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Galon Pinjam</strong></label>
                        <p class="form-control-static" id="rent"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Galon Beli</strong></label>
                        <p class="form-control-static" id="purchase"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Galon Tukar Non-Ervill</strong></label>
                        <p class="form-control-static" id="non_ervill"></p>
                    </div>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                </div>
        </div>
      </div>
    </div>

    <script>
        $(document).ready(function () {
            // Init
            var customers = {!! $customers->toJson() !!};

            $('#new-customer-input').hide();
            $('.input-forms').hide();

            $('#customer-table').on('click','.confirm-btn',function(){
                $('#rent').text('');
                $('#purchase').text('');
                $('#non_ervill').text('');
                for(var i in customers){
                    if(customers[i].id==$(this).data('index')){
                        $('#rent').text(customers[i].rent_qty ? customers[i].rent_qty : 0);
                        $('#purchase').text(customers[i].purchase_qty ? customers[i].purchase_qty : 0);
                        $('#non_ervill').text(customers[i].non_erv_qty ? customers[i].non_erv_qty : 0);
                    }
                }
            });

            $('#customer-table').dataTable({
                fixedHeader: {
                    headerOffset: $('.site-header').outerHeight()
                },
                data:customers,
                columns: [
                    {data: null,
                    render: function (data, type, row, meta) {
                        return '<input class="radio customer-id" type="radio" name="customer_id" value="'+data.id+'">';
                    }},
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'address'},
                    {data: 'phone'},
                    {data: 'type',
                        render: function(data) {
                            if(data == "end_customer")
                                return "End Customer";
                            else if(data == "agent")
                                return "Agen";

                            return "-";
                        }
                    },
                    {data: null,
                    render: function (data, type, row, meta) {
                        return '<button class="btn btn-sm confirm-btn" type="button" data-toggle="modal" data-target="#assetModal" data-index="' + row.id + '">Lihat Aset</button>';
                    }},
                ],
                processing: true,
                'order':[1, 'desc']
            });

            $('#new-customer').on('change', function () {
                $('.quantity').val('');
                $('#is_free').prop('checked', false);
                $('#is_piutang').prop('checked', false);
                if(this.checked){
                    $(this).val(1);
                    $('#new-customer-input').fadeIn();
                    $('.customer-table-container').fadeOut();
                    $('#customer-table .customer-id').prop('checked', false);
                    $('.input-forms').fadeIn();
                    $('.refill').fadeOut();
                    $('.pay-gallon').fadeOut();
                }
                else{
                    $(this).val("");
                    $('#new-customer-input').fadeOut();
                    $('.input-forms').fadeOut();
                    $('.customer-table-container').fadeIn();
                    $('.refill').show();
                    $('.pay-gallon').show();
                }
            });

            //get max quantity for specific customer
            $('#customer-table').on('click', '.customer-id', function () {
                $('.input-forms').fadeIn();
                $('.refill').fadeIn();
                $('.pay-gallon').fadeIn();
                for(var i in customers){
                    if(customers[i].id == $(this).val()){
                        $('.quantity').val('');
                        $('#refill-qty').val('');
                        var rent_qty = customers[i].rent_qty ? customers[i].rent_qty : 0;
                        var purchase_qty = customers[i].purchase_qty ? customers[i].purchase_qty : 0;
                        var non_erv_qty = customers[i].non_erv_qty ? customers[i].non_erv_qty : 0;
                        var max_refill = rent_qty + purchase_qty + non_erv_qty;
                        $('#refill-qty').attr('placeholder','Jumlah (Maks: '+ max_refill + ')');
                        $('#refill-qty').attr('max', max_refill);

                        $('#pay-qty').val('');
                        $('#pay-qty').attr('placeholder','Jumlah (Maks: '+rent_qty + ')');
                        $('#pay-qty').attr('max', rent_qty);
                    }
                }
            });

            $('#is_free').on('change', function () {
                if(this.checked){
                    $('#is_piutang').prop('checked', false);
                    $('#is_piutang_div').fadeOut();
                }
                else{
                    $('#is_piutang_div').fadeIn();
                }
            });

            $('#is_piutang').on('change', function () {
                if(this.checked){
                    $('#is_free').prop('checked', false);
                    $('#is_free_div').fadeOut();
                }
                else{
                    $('#is_free_div').fadeIn();
                }
            });
        });
    </script>

@endsection