@extends('layouts.master')

@section('title')
List Pesanan Customer
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <!--<h3 class="panel-title">Pesanan Air</h3>-->
                <a href="{{route('order.customer.make')}}"><button class="btn btn-primary">Pesan Customer</button></a>               
            </header>

            <table class="table table-hover" id="water_order">
                <thead>
                <th>ID</th>
                <th>Admin</th>
                <th>Customer</th>
                <th>Alamat Customer</th>
                <th>Jumlah (Galon)</th> 
                <th>Jumlah Galon Kosong (Galon)</th>                
                <th>Tgl Order</th>
                <th>Tgl Pengiriman</th>
                <th>Tgl Penerimaan</th>
                <th>Action</th>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>Abi</td>
                    <td>Customer 1</td>
                    <td>Cimone Raya blok C4 nomor 4, Cimone, Tangerang</td>
                    <td>3</td>
                    <td>3</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>                    	
                    	<button class="btn btn-sm">Edit</button>
                    	<button class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Abi</td>
                    <td>Customer 2</td>
                    <td>Serpong Paradise blok G4 nomor 43, Serpong, Tangerang</td>
                    <td>4</td>
                    <td>4</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>                      
                        <button class="btn btn-sm">Edit</button>
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
               <tr>
                    <td>1</td>
                    <td>Charlie</td>
                    <td>Customer 2</td>
                    <td>Serpong Paradise blok G4 nomor 43, Serpong, Tangerang</td>
                    <td>4</td>
                    <td>0</td>
                    <td>20/10/2017 08:20:55</td>
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
            $('#water_order').dataTable({
                'order':[5, 'asc']
            });
        });
    </script>

@endsection