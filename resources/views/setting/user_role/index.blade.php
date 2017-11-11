@extends('layouts.master')

@section('title')
List User Role
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                {{--<h3 class="panel-title"></h3>--}}
                <a href="{{route('setting.user_role.make')}}"><button class="btn btn-primary">Tambah User Role</button></a>               
            </header>
            <table class="table table-hover" id="setting_user_role">
                <thead>
                <th>ID</th>
                <th>Nama</th>               
                <th>Tgl Pembuatan</th>
                <th>Tgl Update</th>     
                <th>Action</th>    
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>Owner</td>                          
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>   
                     <td>                      
                        <button class="btn btn-sm" type="button" data-toggle="modal" data-target="#editModal">Edit</button>
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </td>              
                </tr>
                <tr>
                    <td>2</td>
                    <td>Admin</td>                          
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>   
                     <td>                      
                        <button class="btn btn-sm" type="button" data-toggle="modal" data-target="#editModal">Edit</button>
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </td>              
                </tr>
                <tr>
                    <td>3</td>
                    <td>Driver</td>                          
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>   
                     <td>                      
                        <button class="btn btn-sm" type="button" data-toggle="modal" data-target="#editModal">Edit</button>
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </td>              
                </tr>
                </tbody>
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
                        <label for="name"><strong>Nama</strong></label>
                        <input type="text" class="form-control" name="name">
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

    <script>
        $(document).ready(function () {
            $('#setting_user_role').dataTable({
                scrollX: true,  
                fixedHeader: true,       
                processing: true,
                'order':[2, 'asc']
            });
        });
    </script>
@endsection