@extends('layouts.master')

@section('title')
List Pesanan Air
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <!--<h3 class="panel-title">Pesanan Air</h3>-->
                <a href="{{route('order.water.make')}}"><button class="btn btn-primary">Pesan Air</button></a>               
            </header>

            <table class="table table-hover" id="water_order">
                <thead>
                <th>Status</th>
                <th>ID</th>
                <th>Admin</th>
                {{--<th>Outsourcing Air</th>--}}
                <th>Outsourcing Pengemudi</th>
                <th>Pengemudi</th>
                <th>Jumlah Galon Buffer</th>
                <th>Jumlah Galon Gudang</th>
                <th>Tgl Order</th>
                <th>Tgl Pengiriman</th>
                <th>Tgl Penerimaan</th>
                <th>Action</th>
                </thead>                
            </table>
            
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
          <form action="{{route('order.water.do.confirm')}}" method="POST">
                {{csrf_field()}}
                <input type="hidden" name="id" value="" id="confirm_id">
              <div class="modal-body">       
                <div class="form-group">
                    <label for="name"><strong>Nama Pengemudi</strong></label>
                    <p class="form-control-static">
                        <input id="driver_name" type="text" class="form-control" name="driver_name" placeholder="Nama Pengemudi">
                    </p> 
                </div>             
              </div>

              <div class="modal-footer">
                <button type="submit" class="btn btn-success">Konfirmasi terima stok</button>
                <a id="issue-btn" class="btn btn-danger" href="#">Ada masalah</a>
              </div>
          </form>

        </div>
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
                          <th>Action</th>
                      </thead>                      
                  </table>  

                  <p>Jumlah Barang yang bermasalah: <span id="issuesTotalQuantity"></span></p>   
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
            <form action="{{route('order.water.do.update')}}" method="POST">
                {{csrf_field()}}
                <input type="hidden" name="id" value="" id="input_id">
                <input type="hidden" name="max_buffer_qty" id="max_buffer_qty">
                <input type="hidden" name="max_warehouse_qty" id="max_warehouse_qty">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="editModalLabel">Edit Data</h4>
                </div>

                <div class="modal-body">                                           
                    {{--<div class="form-group">--}}
                        {{--<label for="outsourcing_water"><strong>Outsourcing Pabrik Air</strong></label>--}}
                        {{--<select id="outsourcing_water" name="outsourcing_water" class="form-control">--}}
                            {{--<option value=""></option>--}}
                            {{--@foreach($outsourcingWaters as $outsourcingWater)--}}
                                {{--<option value="{{$outsourcingWater->id}}">{{$outsourcingWater->name}}</option>           --}}
                            {{--@endforeach--}}
                        {{--</select>--}}
                    {{--</div>  --}}
                    <div class="form-group">
                        <label for="outsourcing_driver"><strong>Outsourcing Driver</strong></label>
                        <select id="outsourcing_driver" name="outsourcing_driver" class="form-control">
                            <option value=""></option>
                            @foreach($outsourcingDrivers as $outsourcingDriver)
                                <option value="{{$outsourcingDriver->id}}">{{$outsourcingDriver->name}}</option>           
                            @endforeach
                        </select>
                    </div>  
                    <div id="driver_name_div" class="form-group">
                        <label for="driver_name"><strong>Nama Pengemudi</strong></label>
                        <input id="driver_name_edit" type="text" class="form-control" name="driver_name">
                    </div>                     
                    <div class="form-group">
                        <label for="buffer_qty"><strong>Jumlah Galon Buffer</strong></label>
                        <input id="buffer_qty" type="number" class="form-control" name="buffer_qty" min="1">
                    </div>
                    <div class="form-group">
                        <label for="warehouse_qty"><strong>Jumlah Galon Gudang</strong></label>
                        <input id="warehouse_qty" type="number" class="form-control" name="warehouse_qty" min="1">
                    </div>
                    <div class="form-group">
                        <label for="delivery_at"><strong>Tgl Pengiriman</strong></label>
                        <input id="delivery_at" type="date" class="form-control" name="delivery_at">
                    </div>                                   
                    <div class="form-group">
                        <label for="description"><strong>Deskripsi Pengubahan Data</strong></label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="cancel-btn" type="button" class="btn btn-info ajax-btn" style="float: left;">Batalkan penerimaan stock</button>
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
            <form action="{{route('order.water.do.delete')}}" method="POST">
                {{csrf_field()}}
                <input type="hidden" name="id" value="" id="delete_id"> 
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

            var orderWaters = [];
            $('#water_order').on('click','.detail-btn',function(){
                var index = $(this).data('index');
                for(var i in orderWaters){
                    if(orderWaters[i].id==index){
                        if(orderWaters[i].accepted_at==null){
                            $('#cancel-btn').css('display','none');
                            $('#driver_name_div').css('display','none');
                        }else{
                            $('#cancel-btn').css('display','inline-block');
                            $('#driver_name_div').css('display','block');
                        }          
//                        $('#outsourcing_water').val(orderWaters[i].outsourcing_water);
                        $('#outsourcing_driver').val(orderWaters[i].outsourcing_driver);
                        $('#driver_name_edit').val(orderWaters[i].driver_name);
                        $('#buffer_qty').val(orderWaters[i].buffer_qty);
                        $('#buffer_qty').attr('max',{{$max_buffer_qty}}+orderWaters[i].buffer_qty);
                        $('#max_buffer_qty').val({{$max_buffer_qty}}+orderWaters[i].buffer_qty);
                        $('#warehouse_qty').val(orderWaters[i].warehouse_qty);
                        $('#warehouse_qty').attr('max',{{$max_warehouse_qty}}+orderWaters[i].warehouse_qty);
                        $('#max_warehouse_qty').val({{$max_warehouse_qty}}+orderWaters[i].warehouse_qty);
                        $('#delivery_at').val(moment(orderWaters[i].delivery_at).format('YYYY-MM-DD'));
                        $('#input_id').val(orderWaters[i].id);

                    }
                }

                $('#cancel-btn').on('click',function(){
                    for(var i in orderWaters){
                        if(orderWaters[i].id==index){                            
                            if(orderWaters[i].accepted_at!=null){                               
                               $.ajax({
                                  method: "POST",
                                  url: "{{route('order.water.do.cancel')}}",
                                  data: {id: orderWaters[i].id},
                                  headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                  }
                                })
                                .done(function(data){                              
                                    location.reload();                               
                                })
                                .fail(function(data){
                                    alert('Terjadi kesalahan! Harap cek stok / hubungi admin');                  
                                });
                            }
                            break;
                        }
                    }                
                });
            });

            

            $('#water_order').on('click','.delete-btn',function(){
                for(var i in orderWaters){
                    if(orderWaters[i].id==$(this).data('index')){
                        $('#delete_id').val(orderWaters[i].id);
                    }
                }
            });

            $('#water_order').on('click','.confirm-btn',function(){
                for(var i in orderWaters){
                    if(orderWaters[i].id==$(this).data('index')){
                        $('#confirm_id').val(orderWaters[i].id);

                        $('#issue-btn').attr('href','/order/water/issue/' + orderWaters[i].id);
                    }
                }
            });

            $('#water_order').dataTable({
                scrollX: true,     
                fixedHeader: true,       
                processing: true,
                'order':[7, 'desc'],
                ajax: {
                    url: '/getOrderWaters',
                    dataSrc: ''
                },
                columns: [
                    {
                        data: 'status',
                        render: function ( data, type, row, meta ) {
                            if(row.status == "proses"){
                                return '<td><span class="label label-warning">Proses</span></td>';   
                            }else if(row.status == "bermasalah"){
                                return '<td><span class="label label-danger">Bermasalah</span></td>';                                 
                            }else if(row.status == "selesai"){
                                return '<td><span class="label label-success">Selesai</span></td>';   
                            }                               
                        }
                    },
                    {data: 'id'},
                    {data: 'order.user.full_name'},
//                    {
//                        data: 'outsourcing_water',
//                        render: function ( data ){
//                            if(data!=null){
//                                return data.name;
//                            }else{
//                                return '-';
//                            }
//                        }
//                    },
                    {
                        data: 'outsourcing_driver',
                        render: function ( data ){           
                            if(data!=null){
                                return data.name;
                            }else{
                                return '-';
                            }
                        }
                    },                
                    {
                        data: 'driver_name',
                        render: function ( data ){           
                            if(data!=null){
                                return data;
                            }else{
                                return '-';
                            }
                        }
                    },                  
                    {data: 'buffer_qty'},
                    {data: 'warehouse_qty'},
                    {data: null,
                        render: function (data) {
                            if(data.order.created_at){
                                return moment(data.order.created_at).locale('id').format('DD MMMM YYYY hh:mm:ss');
                            }
                            return '-';
                        }
                    },
                    {data: null,
                        render: function (data) {
                            if(data.delivery_at){
                                return moment(data.delivery_at).locale('id').format('DD MMMM YYYY');
                            }
                            return '-';
                        }
                    },
                    {data: null,
                        render: function (data) {
                            if(data.order.accepted_at){
                                return moment(data.order.accepted_at).locale('id').format('DD MMMM YYYY hh:mm:ss');
                            }
                            return '-';
                        }
                    },
                    {
                        data: null, 
                        render: function ( data, type, row, meta ) {                            
                            //if(row.outsourcing_water!=null){
                                orderWaters.push({
                                    'id': row.id,
                                    'status': row.status,   
//                                    'outsourcing_water': row.outsourcing_water!=null?row.outsourcing_water.id:null,
                                    'outsourcing_driver': row.outsourcing_driver!=null?row.outsourcing_driver.id:null,
                                    'driver_name': row.driver_name,
                                    'buffer_qty': row.buffer_qty,
                                    'warehouse_qty': row.warehouse_qty,
                                    'order_at': row.order.created_at,
                                    'delivery_at': row.delivery_at,
                                    'accepted_at': row.order.accepted_at,
                                    'issues': row.order.issues                            
                                });
                            //}

                            if(row.status == "proses"){
                                return '<button class="btn btn-sm btn-success confirm-btn" type="button" data-toggle="modal" data-target="#confirmModal" data-index="' + row.id + '">Terima Stock</button>'+ 
                                '<button class="btn btn-sm detail-btn" type="button" data-toggle="modal" data-target="#editModal" data-index="' + row.id + '">Edit</button>'+
                                '<button type="button" class="btn btn-sm btn-danger delete-btn" data-toggle="modal" data-target="#deleteModal" data-index="' + row.id + '">Delete</button>';   
                            }else if(row.status == "bermasalah"){
                                return '<button class="btn btn-sm btn-warning issue-detail-btn" data-toggle="modal" data-target="#issueModal" data-index="' + row.id + '">Lihat Masalah</button>  '+ 
                                '<button class="btn btn-sm detail-btn" type="button" data-toggle="modal" data-target="#editModal" data-index="' + row.id + '">Edit</button>'+
                                '<button type="button" class="btn btn-sm btn-danger delete-btn" data-toggle="modal" data-target="#deleteModal" data-index="' + row.id + '">Delete</button>';   
                            }else if(row.status == "selesai"){
                                return '<button class="btn btn-sm detail-btn" type="button" data-toggle="modal" data-target="#editModal" data-index="' + row.id + '">Edit</button>'+
                                '<button type="button" class="btn btn-sm btn-danger delete-btn" data-toggle="modal" data-target="#deleteModal" data-index="' + row.id + '">Delete</button>';   
                            }                               
                        }
                    }                   
                ]
            });

            var issue_detail=[];
            $('#water_order').on('click','.issue-detail-btn',function(){
                issue_detail = [];
                var issuesTotalQuantity = 0;
                $('#issues').dataTable().fnDestroy();

                for(var i in orderWaters){
                    if(orderWaters[i].id==$(this).data('index')){
                        for( var j in orderWaters[i].issues){
                            issue_detail[j] = {
                                'id':orderWaters[i].issues[j].id,
                                'type':orderWaters[i].issues[j].type,
                                'description': orderWaters[i].issues[j].description,
                                'quantity': orderWaters[i].issues[j].quantity
                            };
                            issuesTotalQuantity += orderWaters[i].issues[j].quantity;
                        }
                       
                        $('#issuesTotalQuantity').text(issuesTotalQuantity);
                        //$('#confirm_id').val(orderWaters[i].id);

                        //$('#issue-btn').attr('href','/order/water/issue/' + orderWaters[i].id);
                        break;
                    }
                }

                $('#issues').dataTable({               
                    fixedHeader: true,       
                    processing: true,
                    'order': [0, 'asc'],
                    data: issue_detail,
                    columns: [
                        {data: 'type'},
                        {data: 'description'},
                        {data: 'quantity'},
                        {
                            data: null, 
                            render: function ( data, type, row, meta ) {
                                return '<button type="button" class="btn btn-sm btn-danger delete-issue-btn" data-index="' + row.id + '">Delete</button>';    
                            }
                        }                   
                    ]
                });
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

        });
    </script>

@endsection