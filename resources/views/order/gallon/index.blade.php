@extends('layouts.master')

@section('title')
List Pesanan Galon
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <a href="{{route('order.gallon.make')}}"><button class="btn btn-primary">Pesan Galon</button></a>
                <a href="{{route('order.gallon.inventory')}}"><button class="btn btn-primary">Stock Gudang</button></a>
            </header>
            <table class="table table-hover" id="gallon_order">
                <thead>
                <th>ID</th>
                <th>Admin</th>
                <th>Outsourcing</th>
                <th>Jumlah (Gallon)</th>
                <th align="center">Tgl Order</th>
                <th align="center">Tgl Penerimaan</th>
                <th>Actions</th>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>Ibu Dwi</td>
                    <td>PT Jingkrak</td>
                    <td>200</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 12:20:55</td>
                    <td>
                        <button class="btn btn-sm btn-success" type="button" data-toggle="modal" data-target="#confirmModal">Terima Stock</button>
                        <button class="btn btn-sm" type="button" data-toggle="modal" data-target="#editModal">Edit</button>
                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Ibu Dwi</td>
                    <td>PT Jingkrak</td>
                    <td>350</td>
                    <td>25/10/2017 08:20:55</td>
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
                <form action="{{route('order.gallon.do.confirm')}}" method="POST">
                    {{csrf_field()}}
                    <input type="hidden" name="id" value="" id="confirm_id">
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Konfirmasi terima stok</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Edit Modal -->

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('order.gallon.do.update')}}" method="POST">
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
                            @foreach($outsourcingDrivers as $outsourcingDriver)
                                <option value="{{$outsourcingDriver->id}}">{{$outsourcingDriver->name}}</option>           
                            @endforeach
                        </select> 
                    </div>                      
                    <div class="form-group">
                        <label for="quantity"><strong>Jumlah Galon</strong></label>
                        <input type="number" class="form-control" name="quantity" id="quantity">
                    </div>                   
                    <div class="form-group">
                        <label for="description"><strong>Deskripsi Pengubahan Data</strong></label>
                        <textarea class="form-control" name="description" rows="3" id="description"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="cancel-btn" type="button" class="btn btn-info" style="float: left;">Batalkan penerimaan stock</button>
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
            <form action="{{route('order.gallon.do.delete')}}" method="POST">
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

            var orderGallons = [];
            $('#gallon_order').on('click','.detail-btn',function(){
                var index = $(this).data('index');
                for(var i in orderGallons){
                    if(orderGallons[i].id==index){
                        if(orderGallons[i].accepted_at==null){
                            $('#cancel-btn').css('display','none');
                        }else{
                            $('#cancel-btn').css('display','inline-block');
                        }                        
                        $('#outsourcing').val(orderGallons[i].outsourcing);
                        $('#quantity').val(orderGallons[i].quantity);
                        $('#input_id').val(orderGallons[i].id);

                    }
                }

                $('#cancel-btn').on('click',function(){
                    for(var i in orderGallons){
                        if(orderGallons[i].id==index){
                            if(orderGallons[i].accepted_at!=null){
                               $.ajax({
                                  method: "POST",
                                  url: "{{route('order.gallon.do.cancel')}}",
                                  data: {id: orderGallons[i].id},
                                  headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                  }
                                })
                                .done(function(data){                              
                                    location.reload(); 
                                    //alert('data telah berhasil diupdate');                          
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

            

            $('#gallon_order').on('click','.delete-btn',function(){
                for(var i in orderGallons){
                    if(orderGallons[i].id==$(this).data('index')){
                        $('#delete_id').val(orderGallons[i].id);
                    }
                }
            });

            $('#gallon_order').on('click','.confirm-btn',function(){
                for(var i in orderGallons){
                    if(orderGallons[i].id==$(this).data('index')){
                        $('#confirm_id').val(orderGallons[i].id);
                    }
                }
            });

            $('#gallon_order').dataTable({
                'order':[4, 'desc'],
                scrollX: true,     
                fixedHeader: true,       
                processing: true,
                ajax: {
                    url: '/getOrderGallons',
                    dataSrc: ''
                },
                columns: [
                    {data: 'id'},
                    {data: 'order.user.full_name'},
                    {data: 'outsourcing_driver.name'},
                    {data: 'order.quantity'},
                    {data: 'order.created_at'},
                    {data: 'order.accepted_at'},
                    {
                        data: null, 
                        render: function ( data, type, row, meta ) {
                            orderGallons.push({
                                'id': row.id,
                                'admin': row.order.user.full_name,                               
                                'outsourcing': row.outsourcing_driver.id,
                                'quantity': row.order.quantity,
                                'order_at': row.order.created_at,
                                'accepted_at': row.order.accepted_at                            
                            });

                            if(row.order.accepted_at == null){
                                return '<button class="btn btn-sm btn-success confirm-btn" type="button" data-toggle="modal" data-target="#confirmModal" data-index="' + row.id + '">Terima Stock</button>'+ 
                                '<button class="btn btn-sm detail-btn" type="button" data-toggle="modal" data-target="#editModal" data-index="' + row.id + '">Edit</button>'+
                                '<button type="button" class="btn btn-sm btn-danger delete-btn" data-toggle="modal" data-target="#deleteModal" data-index="' + row.id + '">Delete</button>';   
                            }else{
                                return '<button class="btn btn-sm detail-btn" type="button" data-toggle="modal" data-target="#editModal" data-index="' + row.id + '">Edit</button>'+
                                '<button type="button" class="btn btn-sm btn-danger delete-btn" data-toggle="modal" data-target="#deleteModal" data-index="' + row.id + '">Delete</button>';   
                            }                             
                        }
                    }                   
                ]
            });
        });
    </script>
@endsection