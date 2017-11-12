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
                <th>Jumlah Galon</th>
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
	                    <td>180</td>
	                    <td>8/11/2017</td>
	                    <td>7/11/2017 08:20:55</td>
	                    <td>7/11/2017 08:20:55</td>
	                    <td>
	                    	<a class="btn btn-sm" href="{{route('shipment.track','200')}}">Detail</a>
	                    	<button type="button" class="btn btn-sm" data-toggle="modal" data-target="#editModal">Edit</button>
	                    	<button class="btn btn-sm btn-danger">Delete</button>
	                    </td>
	                </tr>
	                <tr>
	                    <td><span class="label label-info">Draft</span></td>
	                    <td>3</td>
	                    <td>Driver 2</td>
	                    <td>180</td>                
	                    <td>9/11/2017</td>
	                    <td>7/11/2017 11:20:55</td>
	                    <td>7/11/2017 11:20:55</td>
	                    <td>
	                    	<a class="btn btn-sm" href="{{route('shipment.track','200')}}">Detail</a>
	                    	<button type="button" class="btn btn-sm" data-toggle="modal" data-target="#editModal">Edit</button>
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
                <th>Jumlah Galon</th>
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
	                    <td>180</td>   
	                    <td>8/10/2017</td>
	                    <td>6/10/2017 08:20:55</td>
	                    <td>6/10/2017 08:20:55</td>
	                    <td>
	                    	<a class="btn btn-sm" href="{{route('shipment.track','200')}}">Detail</a>
	                    	<button type="button" class="btn btn-sm" data-toggle="modal" data-target="#editModal">Edit</button>
	                    	<button class="btn btn-sm btn-danger">Delete</button>
	                    </td>
	                </tr>
	                <tr>
	                    <td><span class="label label-success">Selesai</span></td>
	                    <td>5</td>
	                    <td>Driver 1</td>
	                    <td>180</td>
	                    <td>8/10/2017</td>
	                    <td>7/10/2017 08:20:55</td>
	                    <td>7/10/2017 08:20:55</td>
	                    <td>
	                    	<a class="btn btn-sm" href="{{route('shipment.track','200')}}">Detail</a>
	                    	<button type="button" class="btn btn-sm" data-toggle="modal" data-target="#editModal">Edit</button>
	                    	<button class="btn btn-sm btn-danger">Delete</button>
	                    </td>
	                </tr>
	                <tr>
	                    <td><span class="label label-success">Selesai</span></td>
	                    <td>6</td>
	                    <td>Driver 2</td>
	                    <td>180</td>         
	                    <td>9/10/2017</td>
	                    <td>7/10/2017 11:20:55</td>
	                    <td>7/10/2017 11:20:55</td>
	                    <td>
	                    	<a class="btn btn-sm" href="{{route('shipment.track','200')}}">Detail</a>
	                    	<button type="button" class="btn btn-sm" data-toggle="modal" data-target="#editModal">Edit</button>
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
                        <label for="driver_name"><strong>Nama Pengemudi</strong></label>
                        <input type="text" class="form-control" name="driver_name">
                    </div>                                                                          
                    <div class="form-group">
                        <label for="delivery_at"><strong>Tgl Pengiriman</strong></label>
                        <input type="date" class="form-control" name="delivery_at">
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
            $('#unfinished_shipment').dataTable({
                scrollX: true,
                fixedHeader: true,
                processing: true,
                'order':[4, 'asc']
            });

            $('#finished_shipment').dataTable({
                scrollX: true,
                fixedHeader: true,
                processing: true,
                'order':[4, 'desc']
            });
        });
    </script>

@endsection