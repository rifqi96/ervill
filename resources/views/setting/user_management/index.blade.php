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
            <form action="{{route('setting.user_management.do.update')}}" method="POST">
                {{csrf_field()}} 
                <input type="hidden" name="id" value="" id="input_id">  
                        
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="editModalLabel">Edit Data</h4>
                </div>

                <div class="modal-body">       
                    @if(auth()->user()->role->name=='superadmin')                
                        <div class="form-group">
                            <label for="role"><strong>Jenis Role</strong></label>
                            <select id="role" name="role" class="form-control">
                                <option value=""></option>
                                @foreach($roles as $role)
                                    <option value="{{$role->id}}">{{$role->name}}</option>
                                @endforeach
                            </select>
                        </div> 

                    @elseif(auth()->user()->role->name=='admin')
                        <div class="form-group">
                            <label for="role"><strong>Jenis Role</strong></label>
                            <select id="role" name="role" class="form-control">
                                <option value="{{$role->id}}">{{$role->name}}</option>
                            </select>  
                        </div> 
                    @endif
                    <div class="form-group">
                        <label for="username"><strong>Username</strong></label>
                        <input id="username" type="text" class="form-control" name="username">
                    </div>   
                    <div class="form-group">
                        <label for="full_name"><strong>Nama</strong></label>
                        <input id="full_name" type="text" class="form-control" name="full_name">
                    </div>     
                    <div class="form-group">
                        <label for="email"><strong>E-mail</strong></label>
                        <input id="email" type="text" class="form-control" name="email">
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
            var users = [];
            $('#setting_user_management').on('click','.detail-btn',function(){
                
                for(var i in users){
                    if(users[i].id==$(this).data('index')){
                        $('#role').val(users[i].role_id);
                        $('#username').val(users[i].username);
                        $('#full_name').val(users[i].full_name);
                        $('#email').val(users[i].email);
                        $('#phone').val(users[i].phone);
                        $('#input_id').val(users[i].id);
                    }
                }
            });

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
                                users.push({
                                    'id': row.id,
                                    'role_id': row.role.id,
                                    'username': row.username,
                                    'full_name': row.full_name,
                                    'email': row.email,
                                    'phone': row.phone
                                });
                                return '<button class="btn btn-sm detail-btn" type="button" data-toggle="modal" data-target="#editModal" data-index="' + row.id + '">Edit</button>'+
                                    '<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal">Delete</button>';
                            }
                           
                        }
                    }                   
                ]
            });
        });
    </script>
@endsection