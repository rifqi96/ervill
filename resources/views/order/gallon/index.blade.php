@extends('layouts.master')

@section('title')
List Pesanan Galon
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <a href="{{route('order.gallon.make')}}"><button class="btn btn-success">Pesan</button></a>
            </header>
            <table class="table table-hover" id="gallon_order">
                <thead>
                <th>No</th>
                <th>Admin</th>
                <th>No Surat Pembelian</th>
                <th>No Faktur</th>
                <th>Outsourcing Pengemudi</th>
                <th>Pengemudi</th>
                <th>Harga Satuan</th>
                <th>Jumlah (Gallon)</th>
                <th>Total Harga</th>
                <th align="center">Tgl Order</th>
                <th align="center">Tgl Penerimaan</th>
                <th>Aksi</th>
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
                    <div class="modal-body">       
                        <div class="form-group">
                            <label for="name"><strong>Nama Pengemudi</strong></label>
                            <p class="form-control-static">
                                <input id="driver_name" type="text" class="form-control" name="driver_name" placeholder="Nama Pengemudi">
                            </p> 
                        </div>     
                        <div class="form-group">
                            <label for="invoice_no"><strong>No Faktur dari Penjual</strong></label>
                            <p class="form-control-static">
                                <input id="invoice_no" type="text" class="form-control" name="invoice_no" placeholder="No Faktur dari Penjual">
                            </p> 
                        </div> 
                        <div class="form-group">
                            <label for="price"><strong>Harga Satuan</strong></label>
                            <p class="form-control-static">
                                <input id="price" type="text" class="form-control" name="price" placeholder="Harga Satuan">
                            </p> 
                        </div> 
                        <div class="form-group">
                            <label for="total"><strong>Total Harga</strong></label>
                            <p class="form-control-static">
                                <input id="total" type="text" class="form-control" name="total" placeholder="Total Harga">
                            </p> 
                        </div>         
                    </div>
                
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
                        <label for="purchase_invoice_no"><strong>No Surat Pembelian</strong></label>
                        <input type="text" id="purchase_invoice_no" name="purchase_invoice_no" class="form-control">
                    </div>       
                    <div class="form-group" id="invoice_no_edit_div">
                        <label for="invoice_no_edit"><strong>No Faktur</strong></label>
                        <input type="text" id="invoice_no_edit" name="invoice_no_edit" class="form-control">
                    </div>                                    
                    <div class="form-group">
                        <label for="outsourcing"><strong>Outsourcing</strong></label>
                        <select id="outsourcing" name="outsourcing" class="form-control">
                            <option value=""></option>
                            @foreach($outsourcingDrivers as $outsourcingDriver)
                                <option value="{{$outsourcingDriver->id}}">{{$outsourcingDriver->name}}</option>           
                            @endforeach
                        </select> 
                    </div>     
                    <div id="driver_name_div" class="form-group">
                        <label for="driver_name_edit"><strong>Nama Pengemudi</strong></label>
                        <input id="driver_name_edit" type="text" class="form-control" name="driver_name">
                    </div>      
                    <div class="form-group" id="price_edit_div">
                        <label for="price_edit"><strong>Harga Satuan</strong></label>
                        <input type="text" id="price_edit" name="price_edit" class="form-control">
                    </div>                   
                    <div class="form-group">
                        <label for="quantity"><strong>Jumlah Galon</strong></label>
                        <input type="number" class="form-control" name="quantity" id="quantity">
                    </div>    
                    <div class="form-group" id="total_edit_div">
                        <label for="total_edit"><strong>Total Harga</strong></label>
                        <input type="text" id="total_edit" name="total_edit" class="form-control">
                    </div>                
                    <div class="form-group">
                        <label for="description"><strong>Deskripsi Pengubahan Data</strong></label>
                        <textarea class="form-control" name="description" rows="3" id="description"></textarea>
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
                            $('#driver_name_div').css('display','none');
                            $('#invoice_no_edit_div, #price_edit_div, #total_edit_div').css('display','none');
                            
                        }else{
                            $('#cancel-btn').css('display','inline-block');
                            $('#driver_name_div').css('display','block');
                            $('#invoice_no_edit_div, #price_edit_div, #total_edit_div').css('display','block');
                        }                        
                        $('#outsourcing').val(orderGallons[i].outsourcing);
                        $('#driver_name_edit').val(orderGallons[i].driver_name);
                        $('#quantity').val(orderGallons[i].quantity);
                        $('#input_id').val(orderGallons[i].id);
                        $('#purchase_invoice_no').val(orderGallons[i].purchase_invoice_no);
                        $('#invoice_no_edit').val(orderGallons[i].invoice_no);
                        $('#price_edit').val(orderGallons[i].price);
                        $('#total_edit').val(orderGallons[i].total);

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
                order:[0, 'desc'],
//                scrollX: true,
                fixedHeader: {
                    headerOffset: $('.site-header').outerHeight()
                },
                processing: true,
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
                ajax: {
                    url: '/getOrderGallons',
                    dataSrc: ''
                },
                columns: [
                    {data: 'id'},
                    {data: null,
                    render: function (data) {
                        if(data.order.user){
                            return '<a href="/setting/user_management/id/'+data.order.user.id+'" target="_blank" title="Klik untuk lihat">'+data.order.user.full_name+'</a>';
                        }
                        return 'Data admin tidak ditemukan';
                    }},
                    {data: 'purchase_invoice_no'},
                    {data: 'invoice_no'},
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
                    {data: 'price'}, 
                    {data: 'order.quantity'},
                    {data: 'total'},
                    {data: null,
                        render: function (data) {
                            if(data.order.created_at){
                                return moment(data.order.created_at).locale('id').format('DD/MM/YYYY HH:mm:ss');
                            }
                            return '-';
                        }
                    },
                    {data: null,
                        render: function (data) {
                            if(data.order.accepted_at){
                                return moment(data.order.accepted_at).locale('id').format('DD/MM/YYYY HH:mm:ss');
                            }
                            return '-';
                        }
                    },
                    {
                        data: null, 
                        render: function ( data, type, row, meta ) {
                            orderGallons.push({
                                'id': row.id,
                                'admin': row.order.user.full_name,                               
                                'outsourcing': row.outsourcing_driver!=null?row.outsourcing_driver.id:null,
                                'driver_name': row.driver_name,
                                'quantity': row.order.quantity,
                                'order_at': row.order.created_at,
                                'accepted_at': row.order.accepted_at,
                                'purchase_invoice_no': row.purchase_invoice_no,   
                                'invoice_no': row.invoice_no,
                                'price': row.price,
                                'total': row.total                         
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