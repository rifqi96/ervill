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
            <table class="table table-hover" id="setting_outsourcing">
                <thead>
                <th>ID</th>
                <th>Jenis</th>
                <th>Nama</th>               
                <th>Tgl Pembuatan</th>
                <th>Tgl Update</th>     
                <th>Action</th>    
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>Supir Air</td>
                    <td>PT XYZ</td>                 
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>   
                     <td>                      
                        <button class="btn btn-sm">Edit</button>
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </td>              
                </tr>
                <tr>
                    <td>2</td>
                    <td>Pabrik Galon</td>
                    <td>PT Galon ABC</td>                 
                    <td>10/10/2017 18:20:55</td>
                    <td>10/10/2017 18:20:55</td> 
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
            $('#setting_outsourcing').dataTable({
                'order':[3, 'asc']
            });
        });
    </script>
@endsection