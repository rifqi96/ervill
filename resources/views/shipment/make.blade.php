@extends('layouts.master')

@section('title')
Buat Pengiriman
@endsection

@section('content')

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <!--<h3 class="panel-title">Pesan Customer</h3>-->
                <a href="{{route('shipment.index')}}"><button class="btn btn-primary">Lihat List Pengiriman</button></a>     
                <h4 class="panel-title" style="margin-top: 50px;">List Pesanan Customer</h4> 

            </header>

            <form action="" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
	            <section class="box-typical box-typical-padding">   
	            	<div class="form-group row">
	                        <label class="col-sm-2 form-control-label">Tgl Pengiriman</label>
	                        <div class="col-sm-10">
	                            <p class="form-control-static">
	                                <input type="date" class="form-control" name="delivery_date" placeholder="Tgl Pengiriman" value="{{Carbon\Carbon::now()->toDateString()}}">
	                            </p>                           
	                        </div>
	                    </div>                   
	            </section><!--.box-typical-->

	            <table class="table table-hover" id="customer_order">
	                <thead>    
	                	<th></th>         
		                <th>ID</th>
		                <th>Admin</th>	             
		                <th>Customer</th>
		                <th>Alamat Customer</th>
		                <th>Jumlah (Galon)</th> 
		                <th>Jumlah Galon Kosong (Galon)</th>                
		                <th>Tgl Order</th>
		                <th>Tgl Pengiriman</th>	                   
	                </thead>
	                <tbody>		               
		                <tr>
		                    <td><input type="checkbox" name="2"></td>
		                    <td>2</td>
		                    <td>Abi</td>                 
		                    <td>Customer 2</td>
		                    <td>Serpong Paradise blok G4 nomor 43, Serpong, Tangerang</td>
		                    <td>4</td>
		                    <td>4</td>
		                    <td>7/11/2017 09:20:55</td>
		                    <td>8/11/2017</td>                              
		                </tr>
		               <tr>
		                    <td><input type="checkbox" name="3"></td>
		                    <td>3</td>
		                    <td>Charlie</td>         
		                    <td>Customer 2</td>
		                    <td>Serpong Paradise blok G4 nomor 43, Serpong, Tangerang</td>
		                    <td>4</td>
		                    <td>0</td>
		                    <td>6/11/2017 11:25:32</td>
		                    <td>8/11/2017</td>                               
		                </tr>
	                </tbody>
	            </table>


            	<p>Jumlah Galon: 180</p>

                <div class="form-group row">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-9">
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#newShipmentModal">Buat jadwal pengiriman</button>
                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#existingShipmentModal">Tambah ke pengiriman</button>
                    </div>
                </div>

                
            	
            

	        	<!-- New Shipment Modal -->

			    <div class="modal fade" id="newShipmentModal" tabindex="-1" role="dialog" aria-labelledby="newShipmentModalLabel">
			      <div class="modal-dialog" role="document">
			        <div class="modal-content">

			          <div class="modal-header">
			            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			            <h4 class="modal-title" id="newShipmentModalLabel">Buat Jadwal Pengiriman</h4>
			          </div>
			     
			              <div class="modal-body">                       
			                <div class="form-group">
			                    <label for="driver_name"><strong>Nama Driver</strong></label>
			                    <p class="form-control-static">
			                        <select id="driver_name" name="driver_name" class="form-control">
			                            <option value=""></option>
			                            <option value="1">Driver 1</option>
			                            <option value="2">Driver 2</option>
			                            <option value="3">Driver 3</option>
			                        </select>
			                    </p> 
			                </div>			                      
			              </div>

			              <div class="modal-footer">
			              	<button type="submit" class="btn btn-success">Submit</button>                
			              </div>
			         

			        </div>
			      </div>
			    </div>


			    <!-- Existing Shipment Modal -->

			    <div class="modal fade" id="existingShipmentModal" tabindex="-1" role="dialog" aria-labelledby="existingShipmentModalLabel">
			      <div class="modal-dialog" role="document">
			        <div class="modal-content">

			          <div class="modal-header">
			            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			            <h4 class="modal-title" id="existingShipmentModalLabel">Tambah ke Pengiriman</h4>
			          </div>
			     
			              <div class="modal-body">                       
			                <table class="table table-hover" id="shipment">
				                <thead>    
				                	<th></th>         
					                <th>ID</th>
					                <th>Nama Pengemudi</th>	            		             
					                <th>Tgl Pengiriman</th>	                   
				                </thead>
				                <tbody>
					                <tr>           
					                	<td><input type="radio" name="chosenShipment" value="1"></td>
					                    <td>11</td>		                              
					                    <td>Driver 1</td>		                    
					                    <td>8/11/2017</td>                                      
					                </tr>
					                <tr>           
					                	<td><input type="radio" name="chosenShipment" value="2"></td>
					                    <td>2</td>		                              
					                    <td>Driver 1</td>		                    
					                    <td>8/11/2017</td>                                      
					                </tr>					              
				                </tbody>
				            </table>          
			              </div>

			              <div class="modal-footer">              
			                <button type="submit" class="btn btn-success">Submit</button> 
			              </div>		         

			        </div>
			      </div>
			    </div>

            </form>
        </div>
    </div>

    

    


    <script>
        $(document).ready(function () {
            $('#customer_order').dataTable({
                scrollX: true,  
                fixedHeader: true,       
                processing: true,
                'order':[5, 'desc']
            });

            $('#shipment').dataTable({
                scrollX: true,  
                fixedHeader: true,       
                processing: true,
                'order':[3, 'asc']
            });
        });
    </script>

@endsection