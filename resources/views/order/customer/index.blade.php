@extends('layouts.master')

@section('title')
List Pesanan Customer
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <!--<h3 class="panel-title">Pesanan Air</h3>-->
                <a href="{{route('order.customer.make')}}"><button class="btn btn-primary">Pesan Customer</button></a>               
            </header>

            <table class="table table-hover" id="customer-order">
                <thead>
                <th>Status</th>
                <th>No</th>
                <th>Nama Customer</th>
                <th>No. Telepon</th>
                <th>Alamat Customer</th>
                <th>Nama Pengemudi</th>
                <th>Jumlah (Galon)</th> 
                <th>Jumlah Galon Kosong (Galon)</th>                
                <th>Tgl Order</th>
                <th>Tgl Pengiriman</th>
                <th>Tgl Penerimaan</th>
                <th>Admin</th>
                <th>Aksi</th>
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
                    <div class="form-group row customer-table-container" >
                        <div class="col-sm-12">
                            <h4 class="box-typical-header"><label for="existingCustomerTable" class="form-control-label">Ganti customer</label></h4>
                            <table id="customer-table">
                                <thead>
                                <th></th>
                                <th>No</th>
                                <th>Nama Customer</th>
                                <th>Alamat</th>
                                <th>No. Telepon</th>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="form-group" id="purchase_type_div">
                        <label for="purchase_type"><strong>Jenis Pembelian</strong></label>
                        <select id="purchase_type" name="purchase_type" class="form-control">
                            <option value="">--</option>
                            <option value="rent">Sewa Galon</option>
                            <option value="purchase">Beli Galon</option>      
                            <option value="non_ervill">Tukar Galon Non-Ervill</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quantity"><strong>Jumlah Galon </strong><span id="edit-qty-max"></span></label>
                        <input type="number" class="form-control" name="quantity" id="edit-qty" placeholder="" max="" min="1">
                    </div>
                    <div class="form-group" id="add_gallon_checkbox_div">
                        <label for="add_gallon"><strong>Tambah Galon ?</strong></label>
                        <input type="checkbox" class="form-control checkbox" name="add_gallon" id="add_gallon" value="add_gallon">
                    </div>

                    <div id="add_gallon_div">
                        <div class="form-group">
                            <label for="add_gallon_purchase_type"><strong>Jenis Pembelian Galon Tambah</strong></label>
                            <select id="add_gallon_purchase_type" name="add_gallon_purchase_type" class="form-control">
                                <option value="">--</option>
                                <option value="rent">Sewa Galon</option>
                                <option value="purchase">Beli Galon</option>      
                                <option value="non_ervill">Tukar Galon Non-Ervill</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="add_gallon_quantity"><strong>Jumlah Galon Tambah</strong><span id="add_gallon_quantity_max"></span></label>
                            <input type="number" class="form-control" name="add_gallon_quantity" id="add_gallon_quantity" placeholder="" max="" min="1">
                        </div>
                    </div>
                    
                    {{-- <div class="form-group">
                        <label for="empty_gallon_quantity"><strong>Jumlah Galon Kosong</strong></label>
                        <input type="number" class="form-control" name="empty_gallon_quantity" id="edit-empty-gallon-qty">
                    </div> --}}
                    <div class="form-group edit-delivery-at">
                        <label for="delivery_at"><strong>Tgl Pengiriman</strong></label>
                        <input type="date" class="form-control" name="delivery_at" id="edit-delivery-at">
                    </div>
                    <div class="form-group remove-shipment">
                        <label for="remove-shipment"><strong>Hapus dari pengiriman</strong></label>
                        <input type="checkbox" class="form-control" name="remove_shipment" id="remove-shipment">
                    </div>
                    <!-- <div class="form-group">
                        <label for="status"><strong>Status</strong></label>
                        <select name="status" id="edit-status" class="form-control">
                            <option value="Draft">Draft</option>
                            <option value="Proses">Proses</option>
                            <option value="Bermasalah">Bermasalah</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </div> -->
                    <div class="form-group">
                        <label for="description"><strong>Deskripsi Pengubahan Data</strong></label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    {{csrf_field()}}
                    <input type="hidden" name="id" value="" id="edit-id">
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

    <!-- Add Issue Modal -->

    <div class="modal fade" id="addIssueModal" tabindex="-1" role="dialog" aria-labelledby="addIssueModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('order.customer.do.addIssue')}}" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="addIssueModalLabel">Tambah Masalah</h4>
                </div>

                <div class="modal-body">  
                    <div class="form-group">
                        <label for="type"><strong>Tipe Masalah</strong></label>
                        <select id="type" name="type" class="form-control">
                            <option value="">--</option>
                            <option value="Refund Gallon">Refund Galon</option>
                            <option value="Kesalahan Customer">Kesalahan Customer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="quantity"><strong>Jumlah</strong></label>
                        <input type="number" class="form-control" name="quantity" min="0">
                    </div>                                         
                    <div class="form-group">
                        <label for="description"><strong>Alasan Penambahan Masalah</strong></label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    {{csrf_field()}}
                    <input type="hidden" name="id" value="" id="addIssue_id">
                    <button type="submit" class="btn btn-confirm">Submit</button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
      </div>
    </div>


    

    <script>
        $(document).ready(function () {

            $.ajax({
                url: '/getOrderCustomers',
                type: 'GET',
                dataType: 'json',
                success: function(result){
                    $('#customer-order').dataTable({
                        scrollX: true,
                        fixedHeader: true,
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
                                    if(data.status == "Selesai"){
                                        return '<span class="label label-success">Selesai</span>';
                                    }
                                    else if(data.status == "Proses"){
                                        return '<span class="label label-warning">Proses</span>';
                                    }
                                    else if(data.status == "Bermasalah"){
                                        return '<span class="label label-danger">Bermasalah</span>';
                                    }
                                    else{
                                        return '<span class="label label-info">Draft</span>';
                                    }
                                }},
                            {data: 'id'},
                            {data: null,
                            render: function(data){
                                if(data.customer){
                                    return data.customer.name;
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
                            {data: null,
                                render: function(data){
                                    if(data.shipment){
                                        return data.shipment.user.full_name;
                                    }
                                    else{
                                        return '-';
                                    }
                                }},
                            {data: null,
                                render: function (data) {                                    
                                    return data.additional_quantity+data.order.quantity;
                                }
                            },
                            {data: 'empty_gallon_quantity'},
                            {data: null,
                                render: function (data) {
                                    if(data.order.created_at){
                                        return moment(data.order.created_at).locale('id').format('DD MMMM YYYY HH:mm:ss');
                                    }
                                    return '-';
                                }
                            },
                            {data: null,
                                render: function(data){
                                    return moment(data.delivery_at).locale('id').format('DD MMMM YYYY');
                                }},
                            {data: null,
                                render: function(data){
                                    if(data.order.accepted_at){
                                        return moment(data.order.accepted_at).locale('id').format('DD MMMM YYYY HH:mm:ss');
                                    }
                                    return '-';
                                }},
                            {data: null,
                            render: function(data){
                                if(data.order.user){
                                    return data.order.user.full_name;
                                }
                                return '<i>Data admin tidak ditemukan</i>';
                            }},
                            {data: null,
                                render: function(data, type, row, meta){
                                    var result = "";
                                    if(data.status != "Draft"){
                                        if(data.shipment){
                                            var shipment_url = "{{route("shipment.track", ":id")}}";
                                            shipment_url = shipment_url.replace(':id', data.shipment.id);
                                            result += '<a class="btn btn-sm" href="'+shipment_url+'" target="_blank">Detail</a>';
                                            // if(data.status == "Proses"){
                                            //     result += '<a class="btn btn-sm" href="'+shipment_url+'" target="_blank">Live Tracking</a>';
                                            // }
                                            // else if(data.status == "Bermasalah" || data.status == "Selesai"){
                                            //     result += '<a class="btn btn-sm" href="'+shipment_url+'" target="_blank">Tracking History</a>';
                                            // }
                                        }
                                    }

                                    if(data.order.issues.length > 0){
                                        result += '<button class="btn btn-sm btn-warning issueModal" data-toggle="modal" data-target="#issueModal" data-index="'+data.id+'">Lihat Masalah</button>';
                                    }
                                    if(data.status!= "Draft" && data.status != "Proses"){
                                        result += '<button type="button" class="btn btn-sm btn-info addIssue-modal" data-toggle="modal" data-target="#addIssueModal" data-index="'+data.id+'">Ada masalah</button>';
                                    }

                                    result +=                                        
                                        '<button type="button" class="btn btn-sm edit-modal" data-toggle="modal" data-target="#editModal" data-index="'+data.id+'">Edit</button>' +
                                        '<button type="button" class="btn btn-sm btn-danger delete-modal" data-toggle="modal" data-target="#deleteModal" data-index="'+data.id+'">Delete</button>';

                                    return result;
                                }}
                        ],
                        processing: true,
                        'order':[8, 'desc']
                    });

                    $('#customer-order').on('click', '.issueModal', function () {
                        var issued_gallon_quantity = 0;
                        for(var i in result){
                            if(result[i].id == $(this).data('index')){
                                $('#issues').DataTable().destroy();
                                $('#issues').dataTable({
                                    fixedHeader: true,
                                    processing: true,
                                    data:result[i].order.issues,
                                    columns:[
                                        {data:'type'},
                                        {data:'description'},
                                        {data:'quantity'},
                                        {
                                            data: null, 
                                            render: function ( data, type, row, meta ) {
                                                return '<button type="button" class="btn btn-sm btn-danger delete-issue-btn" data-index="' + row.id + '">Delete</button>';    
                                            }
                                        } 
                                    ]
                                });
                                for(var j in result[i].order.issues){
                                    issued_gallon_quantity += result[i].order.issues[j].quantity;
                                }
                                $('#issued_gallon_quantity').text("Jumlah galon yang bermasalah: " + issued_gallon_quantity);
                            }
                        }
                    });

                    $('#issues').on('click','.delete-issue-btn',function(){
                        var id = $(this).data('index');
          
                        $.ajax({
                          method: "POST",
                          url: "{{route('issue.do.delete')}}",
                          data: {id: id},
                          headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                          }
                        })
                        .done(function(data){                              
                            location.reload();                               
                        })
                        .fail(function(data){
                            alert('Terjadi kesalahan!');                  
                        });        
                    });

                    $('#customer-order').on('click','.delete-modal', function(){
                        $('#delete-id').val($(this).data('index'));
                    });

                    $('#customer-order').on('click','.edit-modal', function(){
                        $('#edit-id').val($(this).data('index'));
                        var order_data = null;
                        for(var i in result){
                            if(result[i].id == $(this).data('index')){
                                order_data = result[i];
                            }
                        }

                        var inventory = JSON.parse('{!! $inventory !!}');

                        //new customer or not
                        if(order_data.is_new=='true'){
                            $('#add_gallon').prop('checked',false);
                            $('#add_gallon_checkbox_div').hide();
                            $('#purchase_type_div').show();
                            $('#purchase_type').val(order_data.purchase_type);
                        }else{
                            $('#add_gallon_checkbox_div').show();
                            $('#purchase_type').val('');
                            $('#purchase_type_div').hide();
                        }

                        //add gallon or not
                        if(order_data.additional_quantity==0){
                            $('#add_gallon').prop('checked',false);
                            $('#add_gallon_div').hide();
                            $('#add_gallon_purchase_type').val('');
                            $('#add_gallon_quantity').val('');
                        }else{
                            $('#add_gallon').prop('checked',true);
                            $('#add_gallon_div').show();
                            $('#add_gallon_purchase_type').val(order_data.purchase_type);
                            $('#add_gallon_quantity').val(order_data.additional_quantity);
                        }

                        //add more gallon
                        $('#add_gallon').on('change', function () {                            
                            //$('#add_gallon_quantity').attr('placeholder','Jumlah Gallon (Stock Gudang: {{$inventory->quantity}})');
                            //$('#add_gallon_quantity').attr('max', {{$inventory->quantity}});
                            if(this.checked){
                                $('#add_gallon_div').fadeIn();  
                                $('#add_gallon_purchase_type').val(order_data.purchase_type);
                                $('#add_gallon_quantity').val(order_data.additional_quantity);
                            }
                            else{
                                $('#add_gallon_div').fadeOut(); 
                                $('#add_gallon_purchase_type').val('');
                                $('#add_gallon_quantity').val('');
                            }
                        });

                        
                        $('#edit-qty').attr('max', (inventory.quantity + order_data.order.quantity));
                        $('#edit-qty').attr('placeholder', 'Jumlah Gallon (Stock Gudang: '+ (inventory.quantity + order_data.order.quantity) +')');
                        $('#edit-qty').val(order_data.order.quantity);
                        //$('#edit-empty-gallon-qty').val(order_data.empty_gallon_quantity);
                        if(order_data.shipment_id){
                            $('.edit-delivery-at').hide();
                            $('#edit-delivery-at').val(moment(order_data.delivery_at).format('YYYY-MM-DD'));
                            $('.remove-shipment').show();
                            $('#remove-shipment').attr('checked', false);
                        }
                        else{
                            $('.edit-delivery-at').show();
                            $('#edit-delivery-at').val(moment(order_data.delivery_at).format('YYYY-MM-DD'));

                            $('.remove-shipment').hide();
                        }
                        // $('#edit-status').val(order_data.status);

                        $('#customer-table').DataTable().destroy();
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
                                        if(data.id == order_data.customer.id){
                                            return '<input class="radio customer-id" type="radio" name="customer_id" value="'+data.id+'" checked>';
                                        }
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
                    });

                    $('#customer-table').on('click','.customer-id', function(){
                        $('#add_gallon_checkbox_div').show();
                        $('#purchase_type').val('');
                        $('#purchase_type_div').hide();
                    });

                    $('#customer-order').on('click','.addIssue-modal', function(){
                        $('#addIssue_id').val($(this).data('index'));
                    });
                }
            });
        });
    </script>

@endsection