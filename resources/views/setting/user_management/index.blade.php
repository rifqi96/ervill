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
                <th>Phone</th>   
                <th>Tgl Pembuatan</th>
                <th>Tgl Update</th>     
                <th>Action</th>    
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>Owner</td>
                    <td>owner</td>
                    <td>Sulhan Syadeli</td> 
                    <td>owner@gmail.com</td>  
                    <td>08230984343</td>              
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>   
                     <td>                      
                        <button class="btn btn-sm">Edit</button>
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </td>              
                </tr>
                <tr>
                    <td>2</td>
                    <td>Admin</td>
                    <td>admin</td>
                    <td>Ervill Admin</td> 
                    <td>admin@gmail.com</td>  
                    <td>0839849839</td>              
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>   
                     <td>                      
                        <button class="btn btn-sm">Edit</button>
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </td>              
                </tr>
                <tr>
                    <td>3</td>
                    <td>Driver</td>
                    <td>driver</td>
                    <td>Ervill Driver</td> 
                    <td>driver@gmail.com</td>  
                    <td>08993483742</td>              
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>   
                     <td>                      
                        <button class="btn btn-sm">Edit</button>
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </td>              
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#setting_user_management').dataTable({
                'order':[6, 'asc']
            });
        });
    </script>
@endsection