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
                <th>Outsourcing Air</th>
                <th>Outsourcing Pengemudi</th>
                <th>Pengemudi</th>
                <th>Jumlah (Galon)</th>                
                <th>Tgl Order</th>
                <th>Tgl Pengiriman</th>
                <th>Tgl Penerimaan</th>
                <th>Action</th>
                </thead>
                <tbody>
                <tr>
                    <td><span class="label label-danger">Bermasalah</span></td>
                    <td>2</td>
                    <td>Abi</td>
                    <td>Outsourcing 1</td>
                    <td>Delta</td>
                    <td>160 (-5)</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>21/10/2017</td>
                    <td>22/10/2017 08:20:55</td>
                    <td>               
                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#issueModal">Lihat Masalah</button>      	
                    	<button class="btn btn-sm" type="button" data-toggle="modal" data-target="#editModal">Edit</button>
                    	<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td><span class="label label-success">Selesai</span></td>
                    <td>1</td>
                    <td>Beta</td>
                    <td>Outsourcing 2</td>
                    <td>Eko</td>
                    <td>160</td>
                    <td>18/10/2017 08:20:55</td>
                    <td>19/10/2017</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>                    	
                    	<button class="btn btn-sm" type="button" data-toggle="modal" data-target="#editModal">Edit</button>
                    	<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal">Delete</button>
                    </td>
                </tr>
               <tr>
                    <td><span class="label label-warning">Proses</span></td>
                    <td>3</td>
                    <td>Charlie</td>
                    <td>Outsourcing 1</td>
                    <td>Delta</td>
                    <td>160</td>
                    <td>21/10/2017 08:20:55</td>
                    <td>22/10/2017</td>
                    <td>-</td>
                    <td>
                    	<button class="btn btn-sm btn-success" type="button" data-toggle="modal" data-target="#confirmModal">Terima Stock</button>
                    	<button class="btn btn-sm" type="button" data-toggle="modal" data-target="#editModal">Edit</button>
                    	<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal">Delete</button>
                    </td>
                </tr>
                </tbody>
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
                        <input type="text" class="form-control" name="driver_name" placeholder="Nama Pengemudi">
                    </p> 
                </div>             
              </div>

              <div class="modal-footer">
                <button type="submit" class="btn btn-success">Konfirmasi terima stok</button>
                <a class="btn btn-danger" href="{{route('order.water.issue',['id' => 3])}}">Ada masalah</a>
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
                      </thead>
                      <tbody>
                          <tr>
                              <td>Tipe 1</td>
                              <td>Saat angkat galon, galon pecah</td>
                              <td>1</td>
                          </tr>
                          <tr>
                              <td>Tipe 2</td>
                              <td>Tisu kurang</td>
                              <td>2</td>
                          </tr>
                          <tr>
                              <td>Tipe 3</td>
                              <td>Segel terbuka</td>
                              <td>2</td>
                          </tr>
                      </tbody>
                  </table>  

                  <p>Jumlah Galon yang bermasalah: 5</p>   
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
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="editModalLabel">Edit Data</h4>
                </div>

                <div class="modal-body">                                           
                    <div class="form-group">
                        <label for="outsourcing"><strong>Outsourcing</strong></label>
                        <select id="outsourcing" name="outsourcing" class="form-control">
                            <option value=""></option>
                            <option value="1">Outsourcing 1</option>
                            <option value="2">Outsourcing 2</option>
                            <option value="3">Outsourcing 3</option>
                        </select>
                    </div>  
                    <div class="form-group">
                        <label for="driver_name"><strong>Nama Pengemudi</strong></label>
                        <input type="text" class="form-control" name="driver_name">
                    </div>                     
                    <div class="form-group">
                        <label for="quantity"><strong>Jumlah Galon</strong></label>
                        <input type="text" class="form-control" name="quantity">
                    </div>                                           
                    <div class="form-group">
                        <label for="delivery_at"><strong>Tgl Pengiriman</strong></label>
                        <input type="text" class="form-control" name="delivery_at">
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
                        }else{
                            $('#cancel-btn').css('display','inline-block');
                        }                        
                        $('#outsourcing').val(orderWaters[i].outsourcing);
                        $('#quantity').val(orderWaters[i].quantity);
                        $('#input_id').val(orderWaters[i].id);

                    }
                }

                $('#cancel-btn').on('click',function(){
                    for(var i in orderWaters){
                        if(orderWaters[i].id==index){
                            if(orderWaters[i].accepted_at!=null){
                               $.ajax({
                                  method: "POST",
                                  url: "{{route('order.gallon.do.cancel')}}",
                                  data: {id: orderWaters[i].id},
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
                    }
                }
            });

            $('#water_order').dataTable({
                scrollX: true,     
                fixedHeader: true,       
                processing: true,
                'order':[6, 'desc'],
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
                    {data: 'outsourcing_water.name'},
                    {data: 'outsourcing_driver.name'},
                    {data: 'driver_name'},
                    {data: 'order.quantity'},
                    {data: 'order.created_at'},
                    {data: 'delivery_at'},
                    {data: 'order.accepted_at'},
                    {
                        data: null, 
                        render: function ( data, type, row, meta ) {
                            orderWaters.push({
                                'id': row.id,
                                'status': row.status,
                                'outsourcing_water': row.outsourcing_water.id,
                                'outsourcing_driver': row.outsourcing_driver.id,
                                'driver_name': row.driver_name,
                                'quantity': row.order.quantity,
                                'order_at': row.order.created_at,
                                'delivery_at': row.delivery_at,
                                'accepted_at': row.order.accepted_at                            
                            });

                            if(row.status == "proses"){
                                return '<button class="btn btn-sm btn-success confirm-btn" type="button" data-toggle="modal" data-target="#confirmModal" data-index="' + row.id + '">Terima Stock</button>'+ 
                                '<button class="btn btn-sm detail-btn" type="button" data-toggle="modal" data-target="#editModal" data-index="' + row.id + '">Edit</button>'+
                                '<button type="button" class="btn btn-sm btn-danger delete-btn" data-toggle="modal" data-target="#deleteModal" data-index="' + row.id + '">Delete</button>';   
                            }else if(row.status == "bermasalah"){
                                return '<button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#issueModal" data-index="' + row.id + '">Lihat Masalah</button>  '+ 
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

            $('#issues').dataTable({               
                fixedHeader: true,       
                processing: true
            });
        });
    </script>

@endsection