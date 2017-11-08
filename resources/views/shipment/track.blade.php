@extends('layouts.master')

@section('title')
Track Pesanan
@endsection

@section('content')

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
            	<a href="{{route('shipment.index')}}"><button class="btn btn-primary">Lihat List Shipment</button></a>
            	<button class="btn btn-info" data-toggle="modal" data-target="#detailModal">Lihat Detil Pesanan</button>
            </header>

            <section class="box-typical box-typical-padding">                        
                
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Order ID</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">3</p>                      
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Nama Pengemudi</label>
                    <div class="col-sm-10">                     
                    	<p class="form-control-static" data-toggle="modal" data-target="#driverModal">
                    		<a href="#"><u>Ervill Driver</u></a>
                    	</p>                                          
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Tgl Pengiriman</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">20/10/2017</p>                     
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Map</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <img src="https://cdn.elegantthemes.com/blog/wp-content/uploads/2016/09/Divi-Google-Maps.png" width="100%" >
                        </p>                        
                    </div>
                </div>
                  
                
            </section><!--.box-typical-->




            
        </div>
    </div>

    <!-- Driver Modal -->
	<div class="modal fade" id="driverModal" tabindex="-1" role="dialog" aria-labelledby="driverModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">

	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="driverModalLabel">Detail Pengemudi</h4>
	      </div>

	      <div class="modal-body">	     
	      	<div class="form-group">
				<label for="name"><strong>Nama</strong></label>
				<p class="form-control-static">
                    Ervil Driver
                </p> 
			</div>
	        <div class="form-group">
				<label for="phone"><strong>Nomor Telepon</strong></label>
				<p class="form-control-static">
                    08348738742
                </p>
			</div>
			<div class="form-group">
				<label for="email"><strong>E-mail</strong></label>
				<p class="form-control-static">
                    driver@gmail.com
                </p>
			</div>		
	      </div>

	      <div class="modal-footer">
	        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
	      </div>

	    </div>
	  </div>
	</div>

	<!-- Detail Modal -->
	<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">

	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="detailModalLabel">Detail Pesanan</h4>
	      </div>

	      <div class="modal-body">
			  <table class="table table-hover" id="customer_order">
				  <thead>
				  <th>Status</th>
				  <th>ID</th>
				  <th>Admin</th>
				  <th>Nama Pengemudi</th>
				  <th>Customer</th>
				  <th>Alamat Customer</th>
				  <th>Jumlah (Galon)</th>
				  <th>Jumlah Galon Kosong (Galon)</th>
				  <th>Tgl Order</th>
				  <th>Tgl Pengiriman</th>
				  <th>Tgl Penerimaan</th>
				  </thead>
				  <tbody>
				  <tr>
					  <td><span class="label label-success">Selesai</span></td>
					  <td>1</td>
					  <td>Abi</td>
					  <td>Ervill Driver 1</td>
					  <td>Customer 1</td>
					  <td>Cimone Raya blok C4 nomor 4, Cimone, Tangerang</td>
					  <td>3</td>
					  <td>3</td>
					  <td>20/10/2017 08:20:55</td>
					  <td>20/10/2017</td>
					  <td>20/10/2017 12:20:55</td>
				  </tr>
				  <tr>
					  <td><span class="label label-danger">Bermasalah</span></td>
					  <td>2</td>
					  <td>Abi</td>
					  <td>Ervill Driver 1</td>
					  <td>Customer 2</td>
					  <td>Serpong Paradise blok G4 nomor 43, Serpong, Tangerang</td>
					  <td>4</td>
					  <td>4</td>
					  <td>20/10/2017 09:20:55</td>
					  <td>20/10/2017</td>
					  <td>20/10/2017 12:20:55</td>
				  </tr>
				  <tr>
					  <td><span class="label label-warning">Proses</span></td>
					  <td>3</td>
					  <td>Charlie</td>
					  <td>Ervill Driver 2</td>
					  <td>Customer 2</td>
					  <td>Serpong Paradise blok G4 nomor 43, Serpong, Tangerang</td>
					  <td>4</td>
					  <td>0</td>
					  <td>20/10/2017 11:25:32</td>
					  <td>20/10/2017</td>
					  <td>-</td>
				  </tr>
				  </tbody>
			  </table>
	      	{{--<div class="form-group">--}}
				{{--<label for="status"><strong>Status</strong></label>--}}
				{{--<p class="form-control-static">--}}
                    {{--<span class="label label-warning">Proses</span>--}}
                {{--</p> --}}
			{{--</div>--}}
	        {{--<div class="form-group">--}}
				{{--<label for="id"><strong>ID</strong></label>--}}
				{{--<p class="form-control-static">--}}
                    {{--3--}}
                {{--</p>--}}
			{{--</div>--}}
			{{--<div class="form-group">--}}
				{{--<label for="admin"><strong>Admin</strong></label>--}}
				{{--<p class="form-control-static">--}}
                    {{--Charlie--}}
                {{--</p>--}}
			{{--</div>				--}}
			{{--<div class="form-group">--}}
				{{--<label for="customer"><strong>Customer</strong></label>--}}
				{{--<p class="form-control-static">--}}
                    {{--Customer 2--}}
                {{--</p>--}}
			{{--</div>		      --}}
	      	{{--<div class="form-group">--}}
				{{--<label for="customer_address"><strong>Alamat Customer</strong></label>--}}
				{{--<p class="form-control-static">--}}
                    {{--Serpong Paradise blok G4 nomor 43, Serpong, Tangerang--}}
                {{--</p>--}}
			{{--</div>--}}
			{{--<div class="form-group">--}}
				{{--<label for="quantity"><strong>Jumlah</strong></label>--}}
				{{--<p class="form-control-static">--}}
                    {{--4--}}
                {{--</p>--}}
			{{--</div>--}}
			{{--<div class="form-group">--}}
				{{--<label for="gallon_empty_quantity"><strong>Jumlah Galon Kosong</strong></label>--}}
				{{--<p class="form-control-static">--}}
                    {{--0--}}
                {{--</p>--}}
			{{--</div>--}}
			{{--<div class="form-group">--}}
				{{--<label for="created_at"><strong>Tanggal Order</strong></label>--}}
				{{--<p class="form-control-static">--}}
                    {{--20/10/2017 11:25:32--}}
                {{--</p>--}}
			{{--</div>--}}
			{{--<div class="form-group">--}}
				{{--<label for="delivery_at"><strong>Tanggal Pengiriman</strong></label>--}}
				{{--<p class="form-control-static">--}}
                    {{--20/10/2017--}}
                {{--</p>--}}
			{{--</div>--}}
		  </div>


	      <div class="modal-footer">
	        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
	      </div>

	    </div>
	  </div>
	</div>

	<script>
        $(document).ready(function () {
            $('#customer_order').dataTable({
                scrollX: true,
                fixedHeader: true,
                processing: true,
                'order':[8, 'desc']
            });
        });
	</script>

@endsection