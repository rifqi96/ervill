@extends('layouts.master')

@section('title')
List Pesanan Customer
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <a href="{{route('order.customer.make')}}"><button class="btn btn-success">Pesan</button></a>
                {{--<a href="{{route('order.customer.trash.index')}}"><button class="btn btn-danger">Daftar Faktur Dihapus</button></a>--}}
                <button class="btn btn-secondary showFilterBy">Kolom Pencarian</button>
            </header>

            <div class="row filterBy">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Kolom pencarian</h5>
                        </div>
                        <div class="card-block">
                            <form id="filterBy">
                                <div class="row form-group">
                                    <div class="col-xl-3">No Faktur:</div>
                                    <div class="col-xl-9">
                                        {{--<input type="text" name="nomor_invoice" class="form-control" id="search-invoiceno" placeholder="OC0000001">--}}
                                        <select name="invoice_no[]" id="search-invoiceno" class="form-control select2" multiple="multiple">
                                            <option value="">-- Silahkan Pilih --</option>
                                            @foreach($invoices as $invoice)
                                                <option value="{{$invoice->id}}">{{$invoice->id}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-xl-3">Nama Customer:</div>
                                    <div class="col-xl-9">
                                        {{--<input type="text" name="customer_name" class="form-control" id="search-cusname" placeholder="Budi">--}}
                                        <select name="customer_id[]" id="search-cusname" class="form-control select2" multiple="multiple">
                                            <option value="">-- Silahkan Pilih --</option>
                                            @foreach($customers as $customer)
                                                <option value="{{$customer->id}}">{{$customer->name}} - {{$customer->phone}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-xl-3">Tgl Pengiriman dari:</div>
                                    <div class="col-xl-4">
                                        <input type="date" name="delivery_start" class="form-control" id="search-date-start" value="">
                                    </div>
                                    <div class="col-xl-1">Sampai dengan:</div>
                                    <div class="col-xl-4">
                                        <input type="date" name="delivery_end" class="form-control" id="search-date-end" value="">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-xl-3"></div>
                                    <div class="col-xl-9">
                                        {{csrf_field()}}
                                        <button type="submit" class="btn btn-primary search-btn">Cari</button>
                                        <button type="reset" class="btn btn-info reset-btn">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <table class="table table-hover" id="customer-order">
                <thead>
                    <th>Aksi</th>
                    <th>Status</th>
                    <th>No Faktur</th>
                    <th>Nama Customer</th>
                    <th>No. Telepon</th>
                    <th>Alamat Customer</th>
                    <th>Galon Isi Keluar</th>
                    <th>Galon Masuk Kosong Ervill</th>
                    <th>Galon Masuk Non Ervill</th>
                    <th>Tgl Pengiriman</th>
                    <th>Tgl Pembuatan</th>
                    <th>Tgl Penerimaan</th>
                    <th>Keterangan</th>
                    <th>Admin</th>
                </thead>
            </table>
        </div>
    </div>


    <!-- Issue Modal -->

    <div class="modal fade" id="issueModal" tabindex="-1" role="dialog" aria-labelledby="issueModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="issueModalLabel">Detail Masalah</h4>
          </div>

              <div class="modal-body">
                <table class="table table-hover" id="issues">
                      <thead>
                          <th>Tipe Masalah</th>
                          <th>Deskripsi Masalah</th>
                          <th>Jumlah</th>
                          <th>Aksi</th>
                      </thead>
                  </table>

                  <div id="issued_gallon_quantity">Jumlah Galon yang bermasalah: 5</div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
              </div>

        </div>
      </div>
    </div>

    <!-- Edit Modal -->

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('order.customer.do.update')}}" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="editModalLabel">Edit Data</h4>
                </div>

                <div class="modal-body">

                    <div class="form-group row">
                        <div class="col-lg-12">
                            <table class="table table-striped table-bordered table-responsive">
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
                                    <p class="form-control-static"><input class="quantity" id="rent-qty" type="number" class="form-control" name="rent_qty" placeholder="Jumlah (Maks: {{$inventory->quantity}})" max="{{$inventory->quantity}}" min=""></p>
                                </td>
                                <td>
                                    <p class="form-control-static"><input class="quantity" id="purchase-qty" type="number" class="form-control" name="purchase_qty" placeholder="Jumlah (Maks: {{$inventory->quantity}})" max="{{$inventory->quantity}}" min=""></p>
                                </td>
                                <td>
                                    <p class="form-control-static"><input class="quantity" id="non-erv-qty" type="number" class="form-control" name="non_erv_qty" placeholder="Jumlah (Maks: {{$inventory->quantity}})" max="{{$inventory->quantity}}" min=""></p>
                                </td>
                                <td class="pay-gallon">
                                    <p class="form-control-static"><input class="quantity" id="pay-qty" type="number" class="form-control" name="pay_qty" placeholder="" max="" min=""></p>
                                </td>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit-additional-price">Harga Satuan Tambahan</label>
                        <input type="number" class="form-control" id="edit-additional-price" name="additional_price" placeholder="Jika ada, tidak wajib diisi. Contoh: -2000 (Artinya harga satuan berkurang Rp 2.000,- dan sebaliknya)">
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

                    <div class="form-group edit-delivery-at">
                        <label for="delivery_at"><strong>Tgl Pengiriman</strong></label>
                        <input type="date" class="form-control" name="delivery_at" id="edit-delivery-at">
                    </div>

                    <div class="form-group">
                        <label for="edit-description">Keterangan tambahan</label>
                        <textarea name="oc_description" class="form-control" id="edit-description" rows="3" placeholder="Keterangan / Pesan / Catatan tambahan. Boleh dikosongkan, tidak wajib diisi."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="description"><strong>Deskripsi Pengubahan Data</strong></label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    {{csrf_field()}}
                    <input type="hidden" name="id" value="" id="edit-id">
                    <input type="hidden" name="customer_id" value="" id="customer-id">
                    <button type="submit" class="btn btn-success">Submit</button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                </div>
            </form>


        </div>
      </div>
    </div>

    <!-- Delete Modal -->

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('order.customer.do.delete')}}" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="deleteModalLabel">Delete Data</h4>
                </div>

                <div class="modal-body">                                           
                    <div class="form-group">
                        <label for="description"><strong>Alasan Menghapus Data</strong></label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    {{csrf_field()}}
                    <input type="hidden" name="id" value="" id="delete-id">
                    <button type="submit" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
      </div>
    </div>

    {{--<!-- Add Issue Modal -->--}}

    {{--<div class="modal fade" id="addIssueModal" tabindex="-1" role="dialog" aria-labelledby="addIssueModalLabel">--}}
      {{--<div class="modal-dialog" role="document">--}}
        {{--<div class="modal-content">--}}
            {{--<form action="{{route('order.customer.do.addIssue')}}" method="POST">--}}
                {{--<div class="modal-header">--}}
                    {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>--}}
                    {{--<h4 class="modal-title" id="addIssueModalLabel">Tambah Masalah</h4>--}}
                {{--</div>--}}

                {{--<div class="modal-body">  --}}
                    {{--<div class="form-group">--}}
                        {{--<label for="type"><strong>Tipe Masalah</strong></label>--}}
                        {{--<select id="type" name="type" class="form-control">--}}
                            {{--<option value="">--</option>--}}
                            {{--<option value="Refund Gallon">Refund Galon</option>--}}
                            {{--<option value="Kesalahan Customer">Kesalahan Customer</option>--}}
                        {{--</select>--}}
                    {{--</div>--}}
                    {{--<div class="form-group">--}}
                        {{--<label for="quantity"><strong>Jumlah</strong></label>--}}
                        {{--<input type="number" class="form-control" name="quantity" min="0">--}}
                    {{--</div>                                         --}}
                    {{--<div class="form-group">--}}
                        {{--<label for="description"><strong>Alasan Penambahan Masalah</strong></label>--}}
                        {{--<textarea class="form-control" name="description" rows="3"></textarea>--}}
                    {{--</div>--}}
                {{--</div>--}}

                {{--<div class="modal-footer">--}}
                    {{--{{csrf_field()}}--}}
                    {{--<input type="hidden" name="id" value="" id="addIssue_id">--}}
                    {{--<button type="submit" class="btn btn-confirm">Submit</button>--}}
                    {{--<button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>--}}
                {{--</div>--}}
            {{--</form>--}}
        {{--</div>--}}
      {{--</div>--}}
    {{--</div>--}}

    <script>
        nprogress.configure({ minimum: 0.2, easing: 'linear', showSpinner:false, trickleSpeed: 100 }); 

        $(document)
            .ajaxStart(nprogress.start)
            .ajaxStop(nprogress.done);

        $(document).ready(function () {

            $('.filterBy').hide();

            $('.showFilterBy').click(function () {
                $('.filterBy').slideToggle();
            });

            $('#filterBy').submit(function (e) {
                e.preventDefault();

                $.post("{{route('order.customer.do.filterby')}}", $(this).serialize())
                    .done(function (result) {
                        console.log('success');
                        console.log(result);
                        customerTable(result);
                        tableContent(result);
                        //nprogress.inc();
                    })
                    .fail(function (msg) {
                        console.log('error');
                        console.log(msg);
                    });
                $(this).find('button[type=submit]').prop('disabled', false);
            });

            $('#filterBy .reset-btn').click(function () {
                $('#search-cusname').val('');
                $('#search-cusname').trigger('change');
                $('#search-invoiceno').val('');
                $('#search-invoiceno').trigger('change');
            });

            // Init //
            var invoices = {!! $invoices->toJson() !!}
            customerTable(invoices);
            tableContent(invoices);
        });

        var customerTable = function (result) {
            $('#customer-order').DataTable().destroy();
            $('#customer-order').dataTable({
                order:[2, 'desc'],
                fixedHeader: {
                    headerOffset: $('.site-header').outerHeight()
                },
                select: {
                    style: 'multi'
                },
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'excel', text:'Simpan ke Excel', className:'btn btn-success btn-sm', exportOptions: {
                        columns: ':visible'
                    }},
                    { extend: 'print', text:'Cetak', className:'btn btn-warning btn-sm', exportOptions: {
                        columns: ':visible'
                    }},
                    { extend: 'colvis', text:'Pilih Kolom', className:'btn btn-default btn-sm'}

                ],
                data:result,
                columns: [
                    {data: null,
                        render: function(data, type, row, meta){
                            var result = '<a href="/invoice/sales/wh/id/'+data.id+'" onclick="window.open(this.href, \'Struk\', \'left=300,top=50,width=800,height=500,toolbar=1,resizable=1, scrollable=1\'); return false;"><button type="button" class="btn btn-sm">Logistik Gudang</button></a>';

                            if(data.status != "Draft"){
                                if(data.shipment){
                                    var shipment_url = "{{route("shipment.track", ":id")}}";
                                    shipment_url = shipment_url.replace(':id', data.shipment.id);
                                    result += '<a class="btn btn-sm" href="'+shipment_url+'" target="_blank">Pengiriman</a>';
                                }
                            }

//                            if(data.order.issues.length > 0){
//                                result += '<button class="btn btn-sm btn-warning issueModal" data-toggle="modal" data-target="#issueModal" data-index="'+data.id+'">Lihat Masalah</button>';
//                            }
//                            if(data.status!= "Draft" && data.status != "Proses"){
//                                result += '<button type="button" class="btn btn-sm btn-info addIssue-modal" data-toggle="modal" data-target="#addIssueModal" data-index="'+data.id+'">Ada masalah</button>';
//                            }

                            result +=
                                '<button type="button" class="btn btn-sm edit-modal" data-toggle="modal" data-target="#editModal" data-index="'+data.id+'">Edit</button>' +
                                '<button type="button" class="btn btn-sm btn-danger delete-modal" data-toggle="modal" data-target="#deleteModal" data-index="'+data.id+'">Delete</button>';

                            return result;
                        }
                    },
                    {data: 'status',
                        render: function(data, type, row, meta){
                            if(data == "Selesai"){
                                return '<span class="label label-success">Selesai</span>';
                            }
                            else if(data == "Proses"){
                                return '<span class="label label-warning">Proses</span>';
                            }
                            else if(data == "Bermasalah"){
                                return '<span class="label label-danger">Bermasalah</span>';
                            }
                            else if(data == "Batal"){
                                return '<span class="label label-danger">Batal</span>';
                            }

                            return '<span class="label label-info">Draft</span>';
                        }},
                    {data: 'id',
                        render: function(data){
                            if(data){
                                return '<a href="/invoice/sales/id/'+data+'" onclick="window.open(this.href, \'Struk\', \'left=300,top=50,width=800,height=500,toolbar=1,resizable=1, scrollable=1\'); return false;">'+data+'</a>';
                            }
                            return '<i>Data nomor faktur tidak ditemukan</i>';
                        }},
                    {data: null,
                        render: function(data){
                            if(data.customer){
                                return '<a href="/setting/customers/id/'+data.customer.id+'" target="_blank">'+data.customer.name+'</a>';
                            }
                            return '<i>Data customer tidak ditemukan</i>';
                        }},
                    {data: null,
                        render: function(data){
                            if(data.customer){
                                return data.customer.phone;
                            }
                            return '<i>Data customer tidak ditemukan</i>';
                        }},
                    {data: null,
                        render: function(data){
                            if(data.customer){
                                return data.customer.address;
                            }
                            return '<i>Data customer tidak ditemukan</i>';
                        }},
                    {data: 'filled_gallon'},
                    {data: 'empty_gallon'},
                    {data: 'non_erv_gallon'},
                    {data: 'delivery_at',
                        render: function(data){
                            return moment(data).locale('id').format('DD/MM/YYYY');
                        }},
                    {data: 'created_at',
                        render: function(data){
                            if(data){
                                return moment(data).locale('id').format('DD/MM/YYYY HH:mm:ss');
                            }
                            return '-';
                        }},
                    {data: 'accepted_at',
                        render: function(data){
                            if(data){
                                return moment(data).locale('id').format('DD/MM/YYYY HH:mm:ss');
                            }
                            return '-';
                        }},
                    {data: 'description'},
                    {data: 'user',
                        render: function(data){
                            if(data){
                                return '<a href="/setting/user_management/id/'+data.id+'" target="_blank" title="Klik untuk lihat">'+data.full_name+'</a>';
                            }
                            return '<i>Data admin tidak ditemukan</i>';
                        }}

                ],
                processing: true
            });
        };

        var tableContent = function (result) {
            {{--$('#customer-order').on('click', '.issueModal', function () {--}}
                {{--var issued_gallon_quantity = 0;--}}
                {{--for(var i in result){--}}
                    {{--if(result[i].id == $(this).data('index')){--}}
                        {{--$('#issues').DataTable().destroy();--}}
                        {{--$('#issues').dataTable({--}}
                            {{--fixedHeader: true,--}}
                            {{--processing: true,--}}
                            {{--data:result[i].order.issues,--}}
                            {{--columns:[--}}
                                {{--{data:'type'},--}}
                                {{--{data:'description'},--}}
                                {{--{data:'quantity'},--}}
                                {{--{--}}
                                    {{--data: null,--}}
                                    {{--render: function ( data, type, row, meta ) {--}}
                                        {{--return '<button type="button" class="btn btn-sm btn-danger delete-issue-btn" data-index="' + row.id + '">Delete</button>';--}}
                                    {{--}--}}
                                {{--}--}}
                            {{--]--}}
                        {{--});--}}
                        {{--for(var j in result[i].order.issues){--}}
                            {{--issued_gallon_quantity += result[i].order.issues[j].quantity;--}}
                        {{--}--}}
                        {{--$('#issued_gallon_quantity').text("Jumlah galon yang bermasalah: " + issued_gallon_quantity);--}}
                    {{--}--}}
                {{--}--}}
            {{--});--}}

            {{--$('#issues').on('click','.delete-issue-btn',function(){--}}
                {{--var id = $(this).data('index');--}}

                {{--$.ajax({--}}
                    {{--method: "POST",--}}
                    {{--url: "{{route('issue.do.delete')}}",--}}
                    {{--data: {id: id},--}}
                    {{--headers: {--}}
                        {{--'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
                    {{--}--}}
                {{--})--}}
                    {{--.done(function(data){--}}
                        {{--location.reload();--}}
                    {{--})--}}
                    {{--.fail(function(data){--}}
                        {{--alert('Terjadi kesalahan!');--}}
                    {{--});--}}
            {{--});--}}

            $('#customer-order').on('click','.delete-modal', function(){
                $('#delete-id').val($(this).data('index'));
            });

            $('#customer-order').on('click','.edit-modal', function(){
                $('#edit-id').val($(this).data('index'));
                var order_data = null;
                $('#refill-qty').val('');
                $('#rent-qty').val('');
                $('#purchase-qty').val('');
                $('#non-erv-qty').val('');
                $('#pay-qty').val('');
                $('#is_piutang_div').show();
                $('#is_free_div').show();
                for(var i in result){
                    if(result[i].id == $(this).data('index')){
                        order_data = result[i];
                    }
                }

                var inventory = {!! $inventory->toJson() !!};

                if(!order_data){
                    return 0;
                }

                if(order_data.refill_qty > 0){
                    $('#refill-qty').val(order_data.refill_qty);
                }
                if(order_data.rent_qty > 0){
                    $('#rent-qty').val(order_data.rent_qty);
                }
                if(order_data.purchase_qty > 0){
                    $('#purchase-qty').val(order_data.purchase_qty);
                }
                if(order_data.non_erv_qty > 0){
                    $('#non-erv-qty').val(order_data.non_erv_qty);
                }
                if(order_data.pay_qty > 0){
                    $('#pay-qty').val(order_data.pay_qty);
                }

                var total_qty = order_data.customer.rent_qty + order_data.customer.purchase_qty + order_data.customer.non_erv_qty;
                $('#refill-qty').attr('max', total_qty);
                $('#refill-qty').attr('placeholder', 'Jumlah (Maks: ' + total_qty + ')');
                $('#pay-qty').attr('max', order_data.customer.rent_qty);
                $('#pay-qty').attr('placeholder', 'Jumlah (Maks: ' + order_data.customer.rent_qty + ')');

                if(order_data.payment_status == "piutang"){
                    $('#is_piutang').prop('checked', true);
                    $('#is_free_div').hide();
                }
                else{
                    if(order_data.is_free == "true"){
                        $('#is_free').prop('checked', true);
                        $('#is_piutang_div').hide();
                    }
                    else{
                        $('#is_piutang').prop('checked', false);
                        $('#is_free').prop('checked', false);
                    }
                }

                if(order_data.shipment_id){
                    $('.edit-delivery-at').hide();
                    $('#edit-delivery-at').val(moment(order_data.delivery_at).format('YYYY-MM-DD'));
                    // $('.remove-shipment').show();
                    // $('#remove-shipment').attr('checked', false);
                }
                else{
                    $('.edit-delivery-at').show();
                    $('#edit-delivery-at').val(moment(order_data.delivery_at).format('YYYY-MM-DD'));

                    //$('.remove-shipment').hide();
                }

                $('#edit-additional-price').val(order_data.additional_price);
                $('#edit-description').val(order_data.description);
            });

            $('#customer-table').on('click','.customer-id', function(){
                $('#add_gallon_checkbox_div').show();
                $('#purchase_type').val('');
                $('#purchase_type_div').hide();
            });

            $('#customer-order').on('click','.addIssue-modal', function(){
                $('#addIssue_id').val($(this).data('index'));
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
        };
    </script>

@endsection