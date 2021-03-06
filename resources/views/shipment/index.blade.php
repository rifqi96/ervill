@extends('layouts.master')

@section('title')
Pengiriman
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <!--<h3 class="panel-title">Pesanan Air</h3>-->
                <a href="{{route('shipment.make')}}"><button class="btn btn-primary">Buat Pengiriman</button></a>
            </header>

            <h4>Pengiriman yang Belum Selesai</h4>

            <table class="table table-hover" id="unfinished-shipment">
                <thead>
                <th>Status</th>
                <th>No</th>
                <th>Nama Pengemudi</th>
                <th>Jumlah Galon</th>
                <th>Tgl Pengiriman</th>
                <th>Tgl Pembuatan</th>
                <th>Tgl Update</th>
                <th>Aksi</th>
                </thead>
            </table>


            <h4>Pengiriman yang Sudah Selesai</h4>

            <table class="table table-hover" id="finished-shipment">
                <thead>
                <th>Status</th>
                <th>No</th>
                <th>Nama Pengemudi</th>
                <th>Jumlah Galon</th>
                <th>Tgl Pengiriman</th>
                <th>Tgl Pembuatan</th>
                <th>Tgl Update</th>
                <th>Aksi</th>
                </thead>
            </table>


        </div>
    </div>

    <!-- Edit Modal -->

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('shipment.do.update')}}" method="POST">
                {{csrf_field()}}
                <input type="hidden" name="shipment_id" class="shipment-id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="editModalLabel">Edit Data</h4>
                </div>

                <div class="modal-body">                                         
                    <div class="form-group">
                        <label for="driver-id"><strong>Nama Pengemudi</strong></label>
                        <p class="form-control-static">
                            <select id="driver-id" name="driver_id" class="form-control"></select>
                        </p>
                    </div>                                                                          
                    <div class="form-group">
                        <label for="delivery-at"><strong>Tgl Pengiriman</strong></label>
                        <input type="date" class="form-control" name="delivery_at" id="delivery-at">
                    </div>
                    <div class="form-group status">
                        <label for="status"><strong>Status</strong></label>
                        <p class="form-control-static">
                            <select id="status" name="status" class="form-control">
                                <option value="Draft">Draft</option>
                                <option value="Proses">Proses</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </p>
                    </div>
                    <div class="form-group">
                        <label for="edit-description"><strong>Alasan Mengubah Data</strong></label>
                        <textarea class="form-control" name="description" rows="3" id="edit-description"></textarea>
                    </div>                 
                </div>

                <div class="modal-footer">
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
            <form action="{{route('shipment.do.delete')}}" method="POST">
                {{csrf_field()}}
                <input type="hidden" name="shipment_id" class="shipment-id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="deleteModalLabel">Delete Data</h4>
                </div>

                <div class="modal-body">                                           
                    <div class="form-group">
                        <label for="delete-description"><strong>Alasan Menghapus Data</strong></label>
                        <textarea class="form-control" name="description" rows="3" id="delete-description"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                </div>
            </form>


        </div>
      </div>
    </div>



    <script>
        $(document).ready(function () {
            // Get Unfinished Shipments //
            var unfinished_shipments = {!! $unfinished_shipments->toJson() !!};

            $('#unfinished-shipment').dataTable({
                fixedHeader: {
                    headerOffset: $('.site-header').outerHeight()
                },
                processing: true,
                order:[1, 'desc'],
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
                data:unfinished_shipments,
                columns:[
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
                            else if(data.status == "Batal"){
                                return '<span class="label label-danger">Batal</span>';
                            }
                            else{
                                return '<span class="label label-info">Draft</span>';
                            }
                        }},
                    {data:'id'},
                    {data:'user',
                        render: function (data) {
                            if(data){
                                return '<a href="/setting/user_management/id/'+data.id+'" target="_blank">'+data.full_name+'</a>';
                            }
                            return 'Data driver tidak ditemukan';
                        }
                    },
                    {data:null,
                        render: function(data){
                            var gallon_total = 0;
                            for(var i in data.oc_header_invoices){
                                for(var j in data.oc_header_invoices[i].order_customers){
                                    if(data.oc_header_invoices[i].order_customers[j].price_id != 5 && data.oc_header_invoices[i].order_customers[j] != 12){
                                        gallon_total += data.oc_header_invoices[i].order_customers[j].quantity;
                                    }
                                }
                            }

                            return gallon_total;
                        }},
                    {data: null,
                        render: function (data) {
                            if(data.delivery_at){
                                return moment(data.delivery_at).locale('id').format('DD/MM/YYYY');
                            }
                            return '-';
                        }
                    },
                    {data: null,
                        render: function (data) {
                            if(data.created_at){
                                return moment(data.created_at).locale('id').format('DD/MM/YYYY HH:mm:ss');
                            }
                            return '-';
                        }
                    },
                    {data: null,
                        render: function (data) {
                            if(data.updated_at){
                                return moment(data.updated_at).locale('id').format('DD/MM/YYYY HH:mm:ss');
                            }
                            return '-';
                        }
                    },
                    {data: null,
                        render: function(data){
                            var shipment_url = "{{route("shipment.track", ":id")}}";
                            shipment_url = shipment_url.replace(':id', data.id);
                            return '<a class="btn btn-sm" href="'+shipment_url+'" target="_blank">Detail</a>' +
                                '<button type="button" class="btn btn-sm edit-modal" data-toggle="modal" data-target="#editModal" data-index="'+data.id+'">Edit</button>' +
                                '<button type="button" class="btn btn-sm btn-danger delete-modal" data-toggle="modal" data-target="#deleteModal" data-index="'+data.id+'">Delete</button>';
                        }}
                ]
            });

            // Get Finished Shipments //
            var finished_shipments = {!! $finished_shipments->toJson() !!};
            $('#finished-shipment').dataTable({
                fixedHeader: {
                    headerOffset: $('.site-header').outerHeight()
                },
                processing: true,
                order:[1, 'desc'],
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
                data:finished_shipments,
                columns:[
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
                    {data:'id'},
                    {data:'user',
                        render: function (data) {
                            if(data){
                                return '<a href="/setting/user_management/id/'+data.id+'" target="_blank">'+data.full_name+'</a>';
                            }
                            return 'Data driver tidak ditemukan';
                        }
                    },
                    {data:null,
                        render: function(data){
                            var gallon_total = 0;
                            for(var i in data.oc_header_invoices){
                                for(var j in data.oc_header_invoices[i].order_customers){
                                    if(data.oc_header_invoices[i].order_customers[j].price_id != 5 && data.oc_header_invoices[i].order_customers[j] != 12){
                                        gallon_total += data.oc_header_invoices[i].order_customers[j].quantity;
                                    }
                                }
                            }

                            return gallon_total;
                        }},
                    {data: null,
                        render: function (data) {
                            if(data.delivery_at){
                                return moment(data.delivery_at).locale('id').format('DD/MM/YYYY');
                            }
                            return '-';
                        }
                    },
                    {data: null,
                        render: function (data) {
                            if(data.created_at){
                                return moment(data.created_at).locale('id').format('DD/MM/YYYY HH:mm:ss');
                            }
                            return '-';
                        }
                    },
                    {data: null,
                        render: function (data) {
                            if(data.updated_at){
                                return moment(data.updated_at).locale('id').format('DD/MM/YYYY HH:mm:ss');
                            }
                            return '-';
                        }
                    },
                    {data: null,
                        render: function(data){
                            var shipment_url = "{{route("shipment.track", ":id")}}";
                            shipment_url = shipment_url.replace(':id', data.id);
                            return '<a class="btn btn-sm" href="'+shipment_url+'" target="_blank">Detail</a>' +
                                '<button type="button" class="btn btn-sm edit-modal finished" data-toggle="modal" data-target="#editModal" data-index="'+data.id+'">Edit</button>' +
                                '<button type="button" class="btn btn-sm btn-danger delete-modal" data-toggle="modal" data-target="#deleteModal" data-index="'+data.id+'">Delete</button>';
                        }}
                ]
            });

            // Get All Drivers for Select Input //
            $.ajax({
                url:'/getAllDrivers',
                type:'get',
                dataType:'json',
                success:function (result) {
                    for(var i in result){
                        $('#driver-id').append('<option value="'+result[i].id+'">'+result[i].full_name+'</option>');
                    }
                }
            });
            
            var editModal = function (result) {
                $('#driver-id').val(result.user.id);
                $('#delivery-at').val(moment(result.delivery_at).format('YYYY-MM-DD'));
                $('.shipment-id').val(result.id);
                $('#status').val(result.status);
            }

            // On Edit Button //
            $('#unfinished-shipment').on('click','.edit-modal,.delete-modal',function(){
                for(var i in unfinished_shipments){
                    if(unfinished_shipments[i].id == $(this).data('index')){
                        $('#editModal .status').show();
                        editModal(unfinished_shipments[i]);
                    }
                }
            });
            $('#finished-shipment').on('click','.edit-modal,.delete-modal',function(){
                for(var i in finished_shipments){
                    if(finished_shipments[i].id == $(this).data('index')){
                        $('#editModal .status').hide();
                        editModal(finished_shipments[i]);
                    }
                }
            });
        });
    </script>

@endsection