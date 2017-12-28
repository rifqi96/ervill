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
					<label class="col-sm-2 form-control-label">Status</label>
					<div class="col-sm-10">
						<p class="form-control-static">
							@if($shipment->status == "Draft")
								<span class="label label-info">Draft</span>
							@elseif($shipment->status == "Proses")
								<span class="label label-warning">Proses</span>
							@elseif($shipment->status == "Selesai")
								<span class="label label-success">Selesai</span>
							@else
								Status tidak ditemukan
							@endif
						</p>
					</div>
				</div>
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Shipment ID</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{$shipment->id}}</p>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Nama Pengemudi</label>
                    <div class="col-sm-10">                     
                    	<p class="form-control-static" data-toggle="modal" data-target="#driverModal">
                    		<a href="#"><u>{{$shipment->user->full_name}}</u></a>
                    	</p>                                          
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Tgl Pengiriman</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">{{Carbon\Carbon::parse($shipment->delivery_at)->format('d-m-Y')}}</p>
                    </div>
                </div>
                {{-- <div class="form-group row">
                    <label class="col-sm-2 form-control-label">Map</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <img src="https://cdn.elegantthemes.com/blog/wp-content/uploads/2016/09/Divi-Google-Maps.png" width="100%" >
                        </p>                        
                    </div>
                </div> --}}
                  
                
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
                    {{$shipment->user->full_name}}
                </p> 
			</div>
	        <div class="form-group">
				<label for="phone"><strong>Nomor Telepon</strong></label>
				<p class="form-control-static">
					{{$shipment->user->phone}}
                </p>
			</div>
			<div class="form-group">
				<label for="email"><strong>E-mail</strong></label>
				<p class="form-control-static">
					<a href="mailto:{{$shipment->user->email}}">{{$shipment->user->email}}</a>
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
			  <table class="table table-hover" id="customer-order">
				  <thead>
				  <th>Status</th>
				  <th>ID</th>
				  <th>Nama Customer</th>
				  <th>No. Telepon</th>
				  <th>Alamat Customer</th>
				  <th>Jumlah (Galon)</th>
				  <th>Jumlah Galon Kosong (Galon)</th>
				  <th>Tgl Order</th>
				  <th>Tgl Pengiriman</th>
				  <th>Tgl Penerimaan</th>
				  <th>Admin</th>
				  </thead>
			  </table>
		  </div>


	      <div class="modal-footer">
	        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
	      </div>

	    </div>
	  </div>
	</div>

	<script>
        $(document).ready(function () {
            $.ajax({
				url:'/getShipmentById/{{$shipment->id}}',
				type:'get',
				dataType:'json',
				success:function (result) {
                    $('#customer-order').dataTable({
                        scrollX: true,
                        fixedHeader: true,
                        processing: true,
                        order:[8, 'desc'],
                        data:result.order_customers,
                        columns:[
                            {data: null,
                                render: function(data, type, row, meta){
                                    if(data.status == "Selesai"){
                                        return '<span class="label label-success">Selesai</span>';
                                    }
                                    else if(data.status == "Proses"){
                                        return '<span class="label label-warning">Proses</span>';
                                    }
                                    else if(data.status == "Bermasalah"){
                                        return '<span class="label label-danger">Bermasalah</span>';
                                    }
                                    else{
                                        return '<span class="label label-info">Draft</span>';
                                    }
                                }},
                            {data: 'id'},
                            {data: null,
                                render: function(data){
                                    if(data.customer){
                                        return data.customer.name;
                                    }
                                    return '<i>Data customer tidak ditemukan</i>';
                                }},
                            {data: null,
                                render: function(data){
                                    if(data.customer){
                                        return data.customer.phone;
                                    }
                                    return '<i>Data customer tidak ditemukan</i>';
                                }},
                            {data: null,
                                render: function(data){
                                    if(data.customer){
                                        return data.customer.address;
                                    }
                                    return '<i>Data customer tidak ditemukan</i>';
                                }},
                            {data: 'order.quantity'},
                            {data: 'empty_gallon_quantity'},
                            {data: null,
                                render: function (data) {
                                    if(data.order.created_at){
                                        return moment(data.order.created_at).format('DD-MM-YYYY hh:mm:ss');
                                    }
                                    return '-';
                                }
                            },
                            {data: null,
                                render: function (data) {
                                    if(data.delivery_at){
                                        return moment(data.delivery_at).format('DD-MM-YYYY');
                                    }
                                    return '-';
                                }
                            },
                            {data: null,
                                render: function (data) {
                                    if(data.order.accepted_at){
                                        return moment(data.order.accepted_at).format('DD-MM-YYYY hh:mm:ss');
                                    }
                                    return '-';
                                }
                            },
                            {data: null,
                                render: function(data){
                                    if(data.order.user){
                                        return data.order.user.full_name;
                                    }
                                    return '<i>Data admin tidak ditemukan</i>';
                                }},
                        ]
                    });
                }
			});
        });
	</script>

@endsection