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

            <form action="{{route('shipment.do.make')}}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
	            <section class="box-typical box-typical-padding">   
	            	<div class="form-group row">
	                        <label class="col-sm-2 form-control-label">Tgl Pengiriman</label>
	                        <div class="col-sm-10">
	                            <p class="form-control-static">
	                                <input type="date" class="form-control" id="delivery_at" name="delivery_at" placeholder="Tgl Pengiriman" value="{{Carbon\Carbon::now()->toDateString()}}">
	                            </p>                           
	                        </div>
	                    </div>                   
	            </section><!--.box-typical-->

	            <table class="table table-hover" id="customer-order">
	                <thead>    
	                	<th><input type="checkbox" class="checkbox" id="select-all"></th>
		                <th>No Faktur</th>
		                <th>Nama Customer</th>
		                <th>Alamat Customer</th>
						<th>No. Telepon</th>
						<th>Tgl Order</th>
	                </thead>
	            </table>

                <div class="form-group row">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-9">
                        <button type="button" class="btn btn-success make-shipment" data-toggle="modal" data-target="#newShipmentModal">Buat jadwal pengiriman</button>
                        <button type="button" class="btn btn-warning add-to-shipment" data-toggle="modal" data-target="#existingShipmentModal">Tambah ke pengiriman</button>
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
			                    <label for="driver-id"><strong>Nama Driver</strong></label>
			                    <p class="form-control-static">
			                        <select id="driver-id" name="driver_id" class="form-control"></select>
			                    </p> 
			                </div>			                      
			              </div>

			              <div class="modal-footer">
			              	<button type="submit" class="btn btn-success make-shipment-submit">Submit</button>
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
					                <th>No</th>
					                <th>Nama Pengemudi</th>	            		             
					                <th>Tgl Pengiriman</th>	                   
				                </thead>
				            </table>          
			              </div>

			              <div class="modal-footer">              
			                <button type="submit" class="btn btn-success add-to-shipment-submit">Submit</button>
			              </div>		         

			        </div>
			      </div>
			    </div>

				<input type="hidden" name="submit_type" class="submit-type" value="">
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Init //
            var delivery_at = $('#delivery_at').val();
            var getUnshippedOrders = function(delivery_at){
                $.ajax({
                    url:'/getUnshippedOrders',
                    data:{
                        'delivery_at':delivery_at,
                        '_token':'{{csrf_token()}}'
                    },
                    type:'post',
                    dataType:'json',
                    success: function(result){
                        $('#customer-order').DataTable().destroy();
                        $('#customer-order').dataTable({
                            fixedHeader: {
                                headerOffset: $('.site-header').outerHeight()
                            },
                            processing: true,
                            order:[0, 'desc'],
							data:result,
							columns:[
								{data:null,
								render:function(data){
                                	return '<input type="checkbox" class="checkbox order-id" name="order_ids[]" value="'+data.id+'">';
								}},
								{data:null,
								render: function (data) {
									if(data.id){
									    var type = data.type==="sales"?'sales':'return';
									    return '<a href="/invoice/'+type+'/id/'+data.id+'" onclick="window.open(this.href, \'Struk\', \'left=300,top=50,width=800,height=500,toolbar=1,resizable=1, scrollable=1\'); return false;">'+data.id+'</a>';
									}
									return 'No faktur tidak ditemukan';
                                }},
								{data:'customer.name'},
								{data:'customer.address'},
                                {data:'customer.phone'},
                                {data: null,
                                    render: function (data) {
                                        if(data.created_at){
                                            return moment(data.created_at).locale('id').format('DD/MM/YYYY HH:mm:ss');
                                        }
                                        return '-';
                                    }
                                }
							]
                        });
                    }
                });
			};
			var getAvailableShipments = function(delivery_at){
                $.ajax({
					url:'/getAvailableShipmentsByDate',
					type:'post',
					dataType:'json',
					data:{
					    delivery_at:delivery_at,
						'_token':'{{csrf_token()}}'
					},
					success: function (result) {
                        $('#shipment').DataTable().destroy();
                        $('#shipment').dataTable({
                            scrollX: true,
                            fixedHeader: true,
                            processing: true,
                            'order':[1, 'desc'],
							data:result,
							columns:[
								{data:null,
								render: function(data){
                                	return '<input type="radio" class="radio shipment-id" name="shipment_id" value="'+data.id+'">';
								}},
								{data:'id'},
								{data:'user.full_name'},
                                {data: null,
                                    render: function (data) {
                                        if(data.delivery_at){
                                            return moment(data.delivery_at).locale('id').format('DD/MM/YYYY');
                                        }
                                        return '-';
                                    }
                                },
							]
                        });
                    }
				});
			};
			var setCustomerId = function($elem, state){
			    if(state == 'checked'){
                    $elem.prop('checked', true);
				}
				else{
                    $elem.prop('checked', false);
				}
			};
			$.ajax({
				url:'/getAllDrivers',
				type:'get',
				dataType:'json',
				success:function (result) {
				    for(var i in result){
                        $('#driver-id').append('<option value="'+result[i].id+'">'+result[i].full_name+'</option>');
					}
                }
			});
			getUnshippedOrders(delivery_at);

            // Handlers //
            $('#delivery_at').on('change', function(){
                delivery_at = $(this).val();
                getUnshippedOrders(delivery_at);
			});
            $('.make-shipment').on('click', function () {

            });
            $('.add-to-shipment').on('click', function(){
                getAvailableShipments(delivery_at);
			});
            $('#select-all').on('change', function(){
                if(this.checked){
                    setCustomerId($('#customer-order').find('.order-id'), 'checked');
				}
				else{
                    setCustomerId($('#customer-order').find('.order-id'), 'unchecked');
				}
			});
            $('.order-id').on('change', function(){
                if(this.checked){
                    setCustomerId($(this), 'checked');
                }
                else{
                    setCustomerId($(this), 'unchecked');
                }
			});
            $('.add-to-shipment-submit').on('click', function(){
                $('.submit-type').val('add');
			});
            $('.make-shipment').on('click', function () {
				$('.submit-type').val('new');
            });
        });
    </script>

@endsection