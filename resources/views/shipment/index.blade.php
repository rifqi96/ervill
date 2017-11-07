@extends('layouts.master')

@section('title')
Pengiriman
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <!--<h3 class="panel-title">Pesanan Air</h3>-->
                <a href="{{route('shipment.make')}}"><button class="btn btn-primary">Buat Pengiriman</button></a>               
            </header>

            <h4>Pengiriman yang Belum Selesai</h4>

            <table class="table table-hover" id="unfinished_shipment">
                <thead>
                <th>Status</th>
                <th>ID</th>
                <th>Nama Pengemudi</th>
                <th>Tgl Pengiriman</th>
                <th>Tgl Pembuatan</th>
                <th>Tgl Update</th>                
                <th>Action</th>
                </thead>
                <tbody>                	
	                <tr>
	                    <td><span class="label label-warning">Proses</span></td>
	                    <td>2</td>
	                    <td>Driver 1</td>
	                    <td>8/11/2017</td>                       
	                    <td>7/11/2017 08:20:55</td>           
	                    <td>7/11/2017 08:20:55</td>
	                    <td>      
	                    	<a class="btn btn-sm" href="{{route('order.customer.track')}}">Detail</a>    
	                    	<button class="btn btn-sm">Edit</button>
	                    	<button class="btn btn-sm btn-danger">Delete</button>
	                    </td>
	                </tr>
	                <tr>
	                    <td><span class="label label-info">Draft</span></td>
	                    <td>3</td>
	                    <td>Driver 2</td>         
	                    <td>9/11/2017</td>        
	                    <td>7/11/2017 11:20:55</td>    
	                    <td>7/11/2017 11:20:55</td>
	                    <td>     
	                    	<a class="btn btn-sm" href="{{route('order.customer.track')}}">Detail</a>
	                    	<button class="btn btn-sm">Edit</button>
	                    	<button class="btn btn-sm btn-danger">Delete</button>
	                    </td>
	                </tr>
	                
               
                </tbody>
            </table>


            <h4>Pengiriman yang Sudah Selesai</h4>

            <table class="table table-hover" id="finished_shipment">
                <thead>
                <th>Status</th>
                <th>ID</th>
                <th>Nama Pengemudi</th>
                <th>Tgl Pengiriman</th>
                <th>Tgl Pembuatan</th>
                <th>Tgl Update</th>                
                <th>Action</th>
                </thead>
                <tbody>
                	<tr>
	                    <td><span class="label label-success">Selesai</span></td>
	                    <td>4</td>
	                    <td>Driver 1</td>   
	                    <td>8/10/2017</td>                 
	                    <td>6/10/2017 08:20:55</td>           
	                    <td>6/10/2017 08:20:55</td>  
	                    <td>	             
	                    	<a class="btn btn-sm" href="{{route('order.customer.track')}}">Detail</a>       	
	                    	<button class="btn btn-sm">Edit</button>
	                    	<button class="btn btn-sm btn-danger">Delete</button>
	                    </td>
	                </tr>
	                <tr>
	                    <td><span class="label label-success">Selesai</span></td>
	                    <td>5</td>
	                    <td>Driver 1</td>
	                    <td>8/10/2017</td>                       
	                    <td>7/10/2017 08:20:55</td>           
	                    <td>7/10/2017 08:20:55</td>
	                    <td>      
	                    	<a class="btn btn-sm" href="{{route('order.customer.track')}}">Detail</a>    
	                    	<button class="btn btn-sm">Edit</button>
	                    	<button class="btn btn-sm btn-danger">Delete</button>
	                    </td>
	                </tr>
	                <tr>
	                    <td><span class="label label-success">Selesai</span></td>
	                    <td>6</td>
	                    <td>Driver 2</td>         
	                    <td>9/10/2017</td>        
	                    <td>7/10/2017 11:20:55</td>    
	                    <td>7/10/2017 11:20:55</td>
	                    <td>     
	                    	<a class="btn btn-sm" href="{{route('order.customer.track')}}">Detail</a>
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
            $('#unfinished_shipment').dataTable({
                scrollX: true,     
                fixedHeader: true,       
                processing: true,
                'order':[3, 'asc']
            });

            $('#finished_shipment').dataTable({
                scrollX: true,     
                fixedHeader: true,       
                processing: true,
                'order':[3, 'desc']
            });
        });
    </script>

@endsection