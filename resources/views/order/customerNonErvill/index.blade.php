@extends('layouts.master')

@section('title')
List Pesanan Customer Non Ervill
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <a href="{{route('order.customerNonErvill.make')}}"><button class="btn btn-success">Pesan</button></a>
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
                    <th>Galon Aqua</th>
                    <th>Galon Non Aqua</th>                    
                    <th>Tgl Order</th>   
                    <th>Tgl Pengiriman</th>                
                    <th>Tgl Penerimaan</th>
                    <th>Keterangan</th>
                    <th>Admin</th>
                </thead>
            </table>
        </div>
    </div>


    <!-- Issue Modal -->

    {{-- <div class="modal fade" id="issueModal" tabindex="-1" role="dialog" aria-labelledby="issueModalLabel">
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
    </div> --}}

    <!-- Edit Modal -->

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('order.customerNonErvill.do.update')}}" method="POST">
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
                                    <th colspan="3" class="pay-gallon" style="text-align:center;">Galon Tambah</th>                                   
                                </tr>
                                <tr>
                                    <th>Galon Aqua</th>
                                    <th>Galon Non Aqua</th>                                  
                                </tr>
                                </thead>
                                <tbody>                               
                                <td>
                                    <p class="form-control-static"><input class="quantity" id="aqua-qty" type="number" class="form-control" name="aqua_qty" placeholder="Jumlah (Maks: {{$non_ervill->quantity}})" max="{{$non_ervill->quantity}}" min=""></p>
                                </td>
                                <td>
                                    <p class="form-control-static"><input class="quantity" id="non_aqua-qty" type="number" class="form-control" name="non_aqua_qty" placeholder="Jumlah (Maks: {{$non_ervill->quantity}})" max="{{$non_ervill->quantity}}" min=""></p>
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
                    
                    <button id="cancel-btn" type="button" class="btn btn-info ajax-btn" style="float: left;">Batalkan penerimaan stock</button>
                  
                    <button type="submit" class="btn btn-success">Submit</button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                </div>
            </form>

            <form id="cancel-form" action="{{route('order.customerNonErvill.do.cancel')}}" method="POST">
                {{csrf_field()}}
                <input type="hidden" name="id" value="" id="edit-id-cancel-form">
            </form>


        </div>
      </div>
    </div>

    <!-- Delete Modal -->

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('order.customerNonErvill.do.delete')}}" method="POST">
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


    <!-- Confirm Modal -->

    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="confirmModalLabel">Terima Stock</h4>
                </div>
                <form action="{{route('order.customerNonErvill.do.confirm')}}" method="POST">
                    {{csrf_field()}}
                    <input type="hidden" name="id" value="" id="confirm_id">
                    <div class="modal-body">       
                        <p><b>Konfirmasi bahwa pihak ketiga sudah menerima barang</b></p>       
                    </div>
                
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Konfirmasi</button>
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
        $(document).ready(function () {

            $('#cancel-btn').click(function(){
                $('#edit-id-cancel-form').val($('#edit-id').val());
                $('#cancel-form').submit();
            });

            $('.filterBy').hide();

            $('.showFilterBy').click(function () {
                $('.filterBy').slideToggle();
            });

            $('#filterBy').submit(function (e) {
                e.preventDefault();

                $.post("{{route('order.customerNonErvill.do.filterby')}}", $(this).serialize())
                    .done(function (result) {
                        console.log('success');
                        console.log(result);
                        customerTable(result);
                        tableContent(result);
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
                            var result = '<a href="/invoice/salesNonErvill/wh/id/'+data.id+'" onclick="window.open(this.href, \'Struk\', \'left=300,top=50,width=800,height=500,toolbar=1,resizable=1, scrollable=1\'); return false;"><button type="button" class="btn btn-sm">Logistik Gudang</button></a>';

                            // if(data.status != "Draft"){
                            //     if(data.shipment){
                            //         var shipment_url = "{{route("shipment.track", ":id")}}";
                            //         shipment_url = shipment_url.replace(':id', data.shipment.id);
                            //         result += '<a class="btn btn-sm" href="'+shipment_url+'" target="_blank">Pengiriman</a>';
                            //     }
                            // }

//                            if(data.order.issues.length > 0){
//                                result += '<button class="btn btn-sm btn-warning issueModal" data-toggle="modal" data-target="#issueModal" data-index="'+data.id+'">Lihat Masalah</button>';
//                            }
//                            if(data.status!= "Draft" && data.status != "Proses"){
//                                result += '<button type="button" class="btn btn-sm btn-info addIssue-modal" data-toggle="modal" data-target="#addIssueModal" data-index="'+data.id+'">Ada masalah</button>';
//                            }

                            if(data.status!="Selesai"){
                                result +=
                                '<button type="button" class="btn btn-sm btn-success confirm-modal" data-toggle="modal" data-target="#confirmModal" data-index="'+data.id+'">Konfirmasi</button>';
                            }
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
                                return '<a href="/invoice/salesNonErvill/id/'+data+'" onclick="window.open(this.href, \'Struk\', \'left=300,top=50,width=800,height=500,toolbar=1,resizable=1, scrollable=1\'); return false;">'+data+'</a>';
                            }
                            return '<i>Data nomor faktur tidak ditemukan</i>';
                        }},
                    {data: null,
                        render: function(data){
                            if(data.customer_non_ervill){
                                return '<a href="/setting/customerNonErvills/id/'+data.customer_non_ervill.id+'" target="_blank">'+data.customer_non_ervill.name+'</a>';
                            }
                            return '<i>Data customer tidak ditemukan</i>';
                        }},
                    {data: null,
                        render: function(data){
                            if(data.customer_non_ervill){
                                return data.customer_non_ervill.phone;
                            }
                            return '<i>Data customer tidak ditemukan</i>';
                        }},
                    {data: null,
                        render: function(data){
                            if(data.customer_non_ervill){
                                return data.customer_non_ervill.address;
                            }
                            return '<i>Data customer tidak ditemukan</i>';
                        }},
                    {data: 'aqua_gallon'},
                    {data: 'non_aqua_gallon'},                   
                    {data: 'created_at',
                        render: function(data){
                            if(data){
                                return moment(data).locale('id').format('DD/MM/YYYY HH:mm:ss');
                            }
                            return '-';
                        }},
                    {data: 'delivery_at',
                        render: function(data){
                            return moment(data).locale('id').format('DD/MM/YYYY');
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

            $('#customer-order').on('click','.confirm-modal', function(){
                $('#confirm_id').val($(this).data('index'));
            });

            $('#customer-order').on('click','.delete-modal', function(){
                $('#delete-id').val($(this).data('index'));
            });

            $('#customer-order').on('click','.edit-modal', function(){
                $('#edit-id').val($(this).data('index'));
                var order_data = null;
                $('#aqua-qty').val('');
                $('#non_aqua-qty').val('');              
                $('#is_piutang_div').show();
             
                for(var i in result){
                    if(result[i].id == $(this).data('index')){
                        order_data = result[i];
                    }
                }

                

                if(order_data.aqua_gallon > 0){
                    $('#aqua-qty').val(order_data.aqua_gallon);
                }
                if(order_data.non_aqua_gallon > 0){
                    $('#non_aqua-qty').val(order_data.non_aqua_gallon);
                }
                

                //var total_qty = order_data.customerNonErvill.aqua_qty + order_data.customerNonErvill.non_aqua_qty;
                $('#aqua-qty').attr('max', {{$non_ervill->quantity}});
                $('#aqua-qty').attr('placeholder', 'Jumlah (Maks: ' + {{$non_ervill->quantity}} + ')');
                $('#non_aqua-qty').attr('max', {{$non_ervill->quantity}});
                $('#non_aqua-qty').attr('placeholder', 'Jumlah (Maks: ' + {{$non_ervill->quantity}} + ')');

                if(order_data.payment_status == "piutang"){
                    $('#is_piutang').prop('checked', true);
                    //$('#is_free_div').hide();
                }
                else{
                    // if(order_data.is_free == "true"){
                    //     $('#is_free').prop('checked', true);
                    //     $('#is_piutang_div').hide();
                    // }
                    // else{
                    $('#is_piutang').prop('checked', false);
                    //     $('#is_free').prop('checked', false);
                    // }
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

            // $('#is_free').on('change', function () {
            //     if(this.checked){
            //         $('#is_piutang').prop('checked', false);
            //         $('#is_piutang_div').fadeOut();
            //     }
            //     else{
            //         $('#is_piutang_div').fadeIn();
            //     }
            // });

            // $('#is_piutang').on('change', function () {
            //     if(this.checked){
            //         $('#is_free').prop('checked', false);
            //         $('#is_free_div').fadeOut();
            //     }
            //     else{
            //         $('#is_free_div').fadeIn();
            //     }
            // });
        };
    </script>

@endsection