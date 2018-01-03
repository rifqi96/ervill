@extends('layouts.master')

@section('title')
List Customer
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                {{--<h3 class="panel-title"></h3>--}}
                <a href="{{route('setting.customers.make')}}"><button class="btn btn-primary">Tambah Customer Baru</button></a>
            </header>
            <table class="table table-hover" id="setting_customers">
                <thead>
                <th>ID</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>No. Telepon</th>
                <th>Jenis</th>            
                <th>Tgl Pembuatan</th>
                <th>Tgl Update</th>     
                <th>Action</th>    
                </thead>
            </table>
        </div>
    </div>

    <!-- Asset Modal -->

    <div class="modal fade" id="assetModal" tabindex="-1" role="dialog" aria-labelledby="assetModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
           
                
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="assetModalLabel">Aset Customer</h4>
                </div>

                <div class="modal-body">                                           
                    <div class="form-group">
                        <label><strong>Galon Sewa</strong></label>
                        <p class="form-control-static" id="rent"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Galon Beli</strong></label>
                        <p class="form-control-static" id="purchase"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Galon Tukar Non-Ervill</strong></label>
                        <p class="form-control-static" id="non_ervill"></p>
                    </div>
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
            <form action="{{route('setting.customers.do.update')}}" method="POST">
                {{csrf_field()}} 
                <input type="hidden" name="id" value="" id="input_id">  
                        
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="editModalLabel">Edit Data</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="name"><strong>Nama</strong></label>
                        <input id="name" type="text" class="form-control" name="name">
                    </div>
                    <div class="form-group">
                        <label for="address"><strong>Alamat</strong></label>
                        <input id="address" type="text" class="form-control" name="address">
                    </div>  
                    <div class="form-group">
                        <label for="phone"><strong>No. Telepon</strong></label>
                        <input id="phone" type="text" class="form-control" name="phone">
                    </div>    
                    <div class="form-group">
                        <label for="description"><strong>Alasan Mengubah Data</strong></label>
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
            <form action="{{route('setting.customers.do.delete')}}" method="POST">
                {{csrf_field()}}
                <input type="hidden" name="data_id" value="">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="deleteModalLabel">Delete Data</h4>
                </div>

                <div class="modal-body">                                           
                    <div class="form-group">
                        <label for="description"><strong>Alasan menghapus data</strong></label>
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
            var customers = [];

            $('#setting_customers').on('click','.confirm-btn',function(){
                $('#rent').text('');
                $('#purchase').text('');
                $('#non_ervill').text('');
                for(var i in customers){
                    if(customers[i].id==$(this).data('index')){
                        for(var j in customers[i].customer_gallons){
                            if(customers[i].customer_gallons[j].type=='rent'){
                                $('#rent').text(customers[i].customer_gallons[j].qty);
                            }else if(customers[i].customer_gallons[j].type=='purchase'){
                                $('#purchase').text(customers[i].customer_gallons[j].qty);
                            }else if(customers[i].customer_gallons[j].type=='non_ervill'){
                                $('#non_ervill').text(customers[i].customer_gallons[j].qty);
                            }
                        }
                        
                    }
                }
            });

            $('#setting_customers').on('click','.detail-btn',function(){
                for(var i in customers){
                    if(customers[i].id==$(this).data('index')){
                        $('#name').val(customers[i].name);
                        $('#address').val(customers[i].address);
                        $('#phone').val(customers[i].phone);
                        $('#input_id').val(customers[i].id);
                    }
                }
            });

            $('#setting_customers').on('click','.delete-btn',function(){
                for(var i in customers){
                    if(customers[i].id==$(this).data('index')){
                        $('input[name=data_id]').val(customers[i].id);
                    }
                }
            });

            $('#setting_customers').dataTable({
                scrollX: true, 
                fixedHeader: true,       
                processing: true,
                order:[5, 'desc'],
                ajax: {
                    url: '/getCustomers',
                    dataSrc: ''
                },
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'address'},
                    {data: 'phone'},
                    {data: 'type',
                        render: function(data) {
                            if(data == "end_customer")
                                return "End Customer";
                            else if(data == "agent")
                                return "Agen";

                            return "-";
                        }
                    },                    
                    {data: null,
                        render: function (data) {
                            if(data.created_at){
                                return moment(data.created_at).locale('id').format('DD MMMM YYYY hh:mm:ss');
                            }
                            return '-';
                        }
                    },
                    {data: null,
                        render: function (data) {
                            if(data.updated_at){
                                return moment(data.updated_at).locale('id').format('DD MMMM YYYY hh:mm:ss');
                            }
                            return '-';
                        }
                    },
                    {
                        data: null, 
                        render: function ( data, type, row, meta ) {
                            customers.push({
                                'id': row.id,
                                'name': row.name,
                                'address': row.address,
                                'phone': row.phone,
                                'customer_gallons': row.customer_gallons
                            });
                            return '<button class="btn btn-sm confirm-btn" type="button" data-toggle="modal" data-target="#assetModal" data-index="' + row.id + '">Lihat Aset</button>'+
                            '<button class="btn btn-sm detail-btn" type="button" data-toggle="modal" data-target="#editModal" data-index="' + row.id + '">Edit</button>'+
                                '<button type="button" class="btn btn-sm btn-danger delete-btn" data-toggle="modal" data-target="#deleteModal" data-index="' + row.id + '">Delete</button>';
                           
                        }
                    }                   
                ]
            });
        });
    </script>
@endsection