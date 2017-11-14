@extends('layouts.master')

@section('title')
List User
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                {{--<h3 class="panel-title"></h3>--}}
                <a href="{{route('setting.user_management.make')}}"><button class="btn btn-primary">Tambah User</button></a>               
            </header>
            <table class="table table-hover" id="setting_user_management">
                <thead>
                <th>ID</th>
                <th>Jenis Role</th>
                <th>Username</th>   
                <th>Nama</th>         
                <th>E-mail</th>
                <th>No. Telepon</th>   
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
                        <label for="role"><strong>Jenis Role</strong></label>
                        <select id="role" name="role" class="form-control">
                            <option value=""></option>
                            <option value="1">Owner</option>
                            <option value="2">Admin</option>
                            <option value="3">Driver</option>
                        </select>
                    </div> 
                    <div class="form-group">
                        <label for="username"><strong>Username</strong></label>
                        <input type="text" class="form-control" name="username">
                    </div>   
                    <div class="form-group">
                        <label for="name"><strong>Nama</strong></label>
                        <input type="text" class="form-control" name="name">
                    </div>     
                    <div class="form-group">
                        <label for="email"><strong>E-mail</strong></label>
                        <input type="text" class="form-control" name="email">
                    </div>  
                    <div class="form-group">
                        <label for="phone"><strong>No. Telepon</strong></label>
                        <input type="text" class="form-control" name="phone">
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
            $('#setting_user_management').dataTable({
                scrollX: true, 
                fixedHeader: true,       
                processing: true,
                order:[6, 'asc'],
                ajax: {
                    url: '/getUsers',
                    dataSrc: ''
                },
                columns: [
                    {data: 'id'},
                    {data: 'role.name'},
                    {data: 'username'},
                    {data: 'full_name'},
                    {data: 'email'},
                    {data: 'phone'},
                    {data: 'created_at'},
                    {data: 'updated_at'},
                    {
                        data: null, 
                        render: function ( data, type, row, meta ) {
                            if((<?php echo strcmp(auth()->user()->role->name,'admin');?> == 0 && row.role.name != 'driver') || 
                                (<?php echo strcmp(auth()->user()->role->name,'driver');?> == 0)
                                ){
                                return '';
                            }else{
                                return '<button class="btn btn-sm" type="button" data-toggle="modal" data-target="#editModal">Edit</button>'+
                                    '<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal">Delete</button>';
                            }
                           
                        }
                    }                   
                ]
            });
        });
    </script>
@endsection