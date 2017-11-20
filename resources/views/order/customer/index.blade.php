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
                <th>ID</th>
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
                <th>Action</th>
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
            <form action="" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="editModalLabel">Edit Data</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group row customer-table-container">
                        <div class="col-sm-12">
                            <h4 class="box-typical-header"><label for="existingCustomerTable" class="form-control-label">Ganti customer</label></h4>
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
                    <div class="form-group">
                        <label for="quantity"><strong>Jumlah Galon</strong></label>
                        <input type="number" class="form-control" name="quantity">
                    </div>
                    <div class="form-group">
                        <label for="empty_gallon_quantity"><strong>Jumlah Galon Kosong</strong></label>
                        <input type="number" class="form-control" name="empty_gallon_quantity">
                    </div>
                    <div class="form-group">
                        <label for="delivery_at"><strong>Tgl Pengiriman</strong></label>
                        <input type="date" class="form-control" name="delivery_at">
                    </div>
                    <div class="form-group">
                        <label for="accepted_at"><strong>Tgl Penerimaan</strong></label>
                        <input type="date" class="form-control" name="accepted_at">
                    </div>
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
            <form action="" method="POST">
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
                            {data: 'customer.name'},
                            {data: 'customer.phone'},
                            {data: 'customer.address'},
                            {data: null,
                                render: function(data){
                                    if(data.shipment){
                                        return data.shipment.user.full_name;
                                    }
                                    else{
                                        return '-';
                                    }
                                }},
                            {data: 'order.quantity'},
                            {data: 'empty_gallon_quantity'},
                            {data: 'order.created_at'},
                            {data: null,
                                render: function(data){
                                    var date = new Date(data.delivery_at);
                                    return date.getFullYear() + '-' + (date.getMonth()+1) + '-' + date.getDate();
                                }},
                            {data: null,
                                render: function(data){
                                    if(data.order.accepted_at){
                                        return data.order.accepted_at;
                                    }
                                    return '-';
                                }},
                            {data: 'order.user.full_name'},
                            {data: null,
                                render: function(data, type, row, meta){
                                    var result = "";
                                    if(data.status != "Draft"){
                                        var shipment_url = "{{route("shipment.track", ":id")}}";
                                        shipment_url = shipment_url.replace(':id', data.shipment.id);
                                        if(data.status == "Proses"){
                                            result += '<a class="btn btn-sm" href="'+shipment_url+'" target="_blank">Live Tracking</a>';
                                        }
                                        else if(data.status == "Bermasalah" || data.status == "Selesai"){
                                            result += '<a class="btn btn-sm" href="'+shipment_url+'" target="_blank">Tracking History</a>';
                                        }
                                    }

                                    if(data.order.issues.length > 0){
                                        result += '<button class="btn btn-sm btn-warning issueModal" data-toggle="modal" data-target="#issueModal" data-index="'+data.id+'">Lihat Masalah</button>';
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

                    $('.issueModal').on('click', function () {
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
                                        {data:'quantity'}
                                    ]
                                });
                                for(var j in result[i].order.issues){
                                    issued_gallon_quantity += result[i].order.issues[j].quantity;
                                }
                                $('#issued_gallon_quantity').text("Jumlah galon yang bermasalah: " + issued_gallon_quantity);
                            }
                        }
                    });

                    $('.delete-modal').on('click', function(){
                        $('#delete-id').val($(this).data('index'));
                    });

                    $('.edit-modal').on('click', function(){
                        $('#edit-id').val($(this).data('index'));
                    });
                }
            });
        });
    </script>

@endsection