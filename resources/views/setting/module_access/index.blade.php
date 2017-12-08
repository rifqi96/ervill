@extends('layouts.master')

@section('title')
Modul Akses
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                             
            </header>
            <table class="table table-hover" id="setting_module_access">
                <thead>
                <th>ID</th>
                <th>Nama Modul</th>
                <th>Nama Role</th>                 
                <th>Tgl Pembuatan</th>
                <th>Tgl Update</th>                       
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>Pesan Galon</td>
                    <td>Owner, Admin</td>                              
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>                                 
                </tr>
                <tr>
                    <td>2</td>
                    <td>Pesan Air</td>
                    <td>Owner, Admin</td>                              
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>                            
                </tr>
                <tr>
                    <td>3</td>
                    <td>Tambah Admin</td>
                    <td>Owner</td>                              
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>                                 
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#setting_module_access').dataTable({
                scrollX: true,   
                fixedHeader: true,       
                processing: true,
                'order':[3, 'asc']
            });
        });
    </script>
@endsection