@extends('layouts.master')

@section('title')
List Outsourcing
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                {{--<h3 class="panel-title"></h3>--}}
                <a href="{{route('setting.outsourcing.make')}}"><button class="btn btn-primary">Tambah Outsourcing</button></a>               
            </header>

            <h4>Outsourcing Pabrik Air</h4>

            <table class="table table-hover" id="setting_outsourcing_water">
                <thead>
                <th>ID</th>
                <th>Nama</th>               
                <th>Tgl Pembuatan</th>
                <th>Tgl Update</th>     
                <th>Action</th>    
                </thead>
            </table>

            <h4>Outsourcing Pengemudi</h4>

            <table class="table table-hover" id="setting_outsourcing_driver">
                <thead>
                <th>ID</th>
                <th>Nama</th>               
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
            <form action="" method="POST" id="editForm">
                {{csrf_field()}}
                <input type="hidden" name="id" value="" id="input_id">  
                                         
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="editModalLabel">Edit Data</h4>
                </div>

                <div class="modal-body">                       
                    <div class="form-group">
                        <label for="name"><strong>Nama</strong></label>
                        <input type="text" class="form-control" name="name" id="name">
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

            var outsourcingWaters = [];
            $('#setting_outsourcing_water').on('click','.detail-btn',function(){
                
                for(var i in outsourcingWaters){
                    if(outsourcingWaters[i].id==$(this).data('index')){                        
                        $('#name').val(outsourcingWaters[i].name);   
                        $('#input_id').val(outsourcingWaters[i].id);                    
                    }
                }

                $('#editForm').attr('action','{{route('setting.outsourcing.do.updateWater')}}');
            });

            var outsourcingDrivers = [];
            $('#setting_outsourcing_driver').on('click','.detail-btn',function(){
                
                for(var i in outsourcingDrivers){
                    if(outsourcingDrivers[i].id==$(this).data('index')){                       
                        $('#name').val(outsourcingDrivers[i].name);    
                        $('#input_id').val(outsourcingDrivers[i].id);                    
                    }
                }

                $('#editForm').attr('action','{{route('setting.outsourcing.do.updateDriver')}}');
            });

            $('#setting_outsourcing_water').dataTable({
                scrollX: true,    
                fixedHeader: true,       
                processing: true,
                'order':[0, 'asc'],
                ajax: {
                    url: '/getOutsourcingWaters',
                    dataSrc: ''
                },
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'created_at'},
                    {data: 'updated_at'},
                    {
                        data: null, 
                        render: function ( data, type, row, meta ) {
                            outsourcingWaters.push({
                                'id': row.id,                               
                                'name': row.name                            
                            });
                            return '<button class="btn btn-sm detail-btn" type="button" data-toggle="modal" data-target="#editModal" data-index="' + row.id + '" data-outsourcing="water">Edit</button>'+
                                '<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal">Delete</button>';
                        }
                    }                   
                ]
            });

            $('#setting_outsourcing_driver').dataTable({
                scrollX: true,    
                fixedHeader: true,       
                processing: true,
                'order':[0, 'asc'],
                ajax: {
                    url: '/getOutsourcingDrivers',
                    dataSrc: ''
                },
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'created_at'},
                    {data: 'updated_at'},
                    {
                        data: null, 
                        render: function ( data, type, row, meta ) {
                            outsourcingDrivers.push({
                                'id': row.id,                               
                                'name': row.name                            
                            });
                            return '<button class="btn btn-sm detail-btn" type="button" data-toggle="modal" data-target="#editModal" data-index="' + row.id + '" data-outsourcing="driver">Edit</button>'+
                                '<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal">Delete</button>';
                        }
                    }                   
                ]
            });
        });
    </script>
@endsection