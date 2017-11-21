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
                <th>ID</th>
                <th>Nama Pengemudi</th>
                <th>Jumlah Galon</th>
                <th>Tgl Pengiriman</th>
                <th>Tgl Pembuatan</th>
                <th>Tgl Update</th>
                <th>Action</th>
                </thead>
            </table>


            <h4>Pengiriman yang Sudah Selesai</h4>

            <table class="table table-hover" id="finished-shipment">
                <thead>
                <th>Status</th>
                <th>ID</th>
                <th>Nama Pengemudi</th>
                <th>Jumlah Galon</th>
                <th>Tgl Pengiriman</th>
                <th>Tgl Pembuatan</th>
                <th>Tgl Update</th>
                <th>Action</th>
                </thead>
            </table>


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
                    <div class="form-group">
                        <label for="driver_name"><strong>Nama Pengemudi</strong></label>
                        <input type="text" class="form-control" name="driver_name">
                    </div>                                                                          
                    <div class="form-group">
                        <label for="delivery_at"><strong>Tgl Pengiriman</strong></label>
                        <input type="date" class="form-control" name="delivery_at">
                    </div>   
                    <div class="form-group">
                        <label for="description"><strong>Deskripsi Pengubahan Data</strong></label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
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
            <form action="" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="deleteModalLabel">Delete Data</h4>
                </div>

                <div class="modal-body">                                           
                    <div class="form-group">
                        <label for="description"><strong>Deskripsi Pengubahan Data</strong></label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
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
            $.ajax({
                url:'/getUnfinishedShipments',
                type:'GET',
                dataType:'json',
                success: function(result){
                    $('#unfinished-shipment').dataTable({
                        scrollX: true,
                        fixedHeader: true,
                        processing: true,
                        'order':[4, 'desc'],
                        data:result,
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
                            {data:'user.full_name'},
                            {data:null,
                            render: function(data){
                                var gallon_total = 0;
                                for(var i in data.orderCustomers){
                                    gallon_total += data.orderCustomers[i].order.quantity;
                                }

                                return gallon_total;
                            }},
                            {data:'delivery_at'},
                            {data:'created_at'},
                            {data:'updated_at'},
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
                }
            });

            $.ajax({
                url:'/getFinishedShipments',
                type:'GET',
                dataType:'json',
                success: function(result){
                    $('#finished-shipment').dataTable({
                        scrollX: true,
                        fixedHeader: true,
                        processing: true,
                        'order':[4, 'desc'],
                        data:result,
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
                            {data:'user.full_name'},
                            {data:null,
                                render: function(data){
                                    var gallon_total = 0;
                                    for(var i in data.orderCustomers){
                                        gallon_total += data.orderCustomers[i].order.quantity;
                                    }

                                    return gallon_total;
                                }},
                            {data:'delivery_at'},
                            {data:'created_at'},
                            {data:'updated_at'},
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
                }
            });
        });
    </script>

@endsection