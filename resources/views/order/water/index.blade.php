@extends('layouts.master')

@section('title')
List Pesanan Air
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <!--<h3 class="panel-title">Pesanan Air</h3>-->
                <a href="{{route('order.water.make')}}"><button class="btn btn-primary">Pesan Air</button></a>               
            </header>

            <table class="table table-hover" id="water_order">
                <thead>
                <th>ID</th>
                <th>Admin</th>
                <th>Outsourcing</th>
                <th>Pengemudi</th>
                <th>Jumlah (Galon)</th>                
                <th>Tgl Order</th>
                <th>Tgl Pengiriman</th>
                <th>Tgl Penerimaan</th>
                <th>Action</th>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>Abi</td>
                    <td>Outsourcing 1</td>
                    <td>Delta</td>
                    <td>160</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>
                    	<a class="btn btn-sm" href="{{route('order.water.issue')}}">Make Issue</a>
                    	<button class="btn btn-sm">Edit</button>
                    	<button class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Beta</td>
                    <td>Outsourcing 2</td>
                    <td>Eko</td>
                    <td>160</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>
                    	<button class="btn btn-sm">Make Issue</button>
                    	<button class="btn btn-sm">Edit</button>
                    	<button class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
               <tr>
                    <td>1</td>
                    <td>Charlie</td>
                    <td>Outsourcing 1</td>
                    <td>Delta</td>
                    <td>155</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>
                    	<button class="btn btn-sm">Make Issue</button>
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
            $('#water_order').dataTable({
                'order':[5, 'asc']
            });
        });
    </script>

@endsection