@extends('layouts.master')

@section('title')
    Daftar Harga
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <h2>Harga Penjualan End Customer</h2>
            </header>
            <table class="table table-hover" id="customer-sale-prices">
                <thead>
                <th>No</th>
                <th>Nama</th>
                <th>Harga</th>
                <th align="center">Tgl Update</th>
                <th>Aksi</th>
                </thead>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <h2>Harga Penjualan Agen</h2>
            </header>
            <table class="table table-hover" id="agent-sale-prices">
                <thead>
                <th>No</th>
                <th>Nama</th>
                <th>Harga</th>
                <th align="center">Tgl Update</th>
                <th>Aksi</th>
                </thead>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <h2>Biaya Retur End Customer</h2>
            </header>
            <table class="table table-hover" id="customer-return-prices">
                <thead>
                <th>No</th>
                <th>Nama</th>
                <th>Harga</th>
                <th align="center">Tgl Update</th>
                <th>Aksi</th>
                </thead>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <h2>Biaya Retur Agen</h2>
            </header>
            <table class="table table-hover" id="agent-return-prices">
                <thead>
                <th>No</th>
                <th>Nama</th>
                <th>Harga</th>
                <th align="center">Tgl Update</th>
                <th>Aksi</th>
                </thead>
            </table>
        </div>
    </div>

    <!-- Edit Modal -->

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{route('price.do.update')}}" method="POST">
                    {{csrf_field()}}
                    <input type="hidden" name="id" value="" id="input_id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="editModalLabel">Edit Data</h4>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="price"><strong>Harga</strong></label>
                            <input id="price" type="text" class="form-control" name="price">
                        </div>
                        <div class="form-group">
                            <label for="description"><strong>Deskripsi Pengubahan Data</strong></label>
                            <textarea id="description" class="form-control" name="description" rows="3"></textarea>
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

            var customer_sale_prices = {!! $customer_sale_prices->toJson() !!};
            var customer_return_prices = {!! $customer_return_prices->toJson() !!};
            var agent_sale_prices = {!! $agent_sale_prices->toJson() !!};
            var agent_return_prices = {!! $agent_return_prices->toJson() !!};

            for(var i=0; i<4; i++){
                var id = "";
                var name = "";
                var data = null;
                if(i==0){
                    id = "customer-sale-prices";
                    name = "customer_sale_prices";
                    data = customer_sale_prices;
                }
                else if(i==1){
                    id = "customer-return-prices";
                    name = "customer_return_prices";
                    data = customer_return_prices;
                }
                else if(i==2){
                    id = "agent-sale-prices";
                    name = "agent_sale_prices";
                    data = agent_sale_prices;
                }
                else{
                    id = "agent-return-prices";
                    name = "agent_return_prices";
                    data = agent_return_prices;
                }

                $('#' + id).dataTable({
                    fixedHeader: {
                        headerOffset: $('.site-header').outerHeight()
                    },
                    processing: true,
                    'order':[0, 'asc'],
                    select: {
                        style: 'multi'
                    },
                    dom: 'Bfrtip',
                    buttons: [
                        { extend: 'excel', text:'Simpan ke Excel', className:'btn btn-success btn-sm', exportOptions: {
                            columns: ':visible'
                        }},
                        { extend: 'print', text:'Cetak', className:'btn btn-warning btn-sm', exportOptions: {
                            columns: ':visible'
                        }},
                        { extend: 'colvis', text:'Pilih Kolom', className:'btn btn-default btn-sm'}

                    ],
                    data:data,
                    columns: [
                        {data: 'id'},
                        {data: 'name'},
                        {data: 'price',
                            render: function (data) {
                                return numeral(data).format('$0,0.00');
                            }},
                        {data: null,
                            render: function (data) {
                                if(data.updated_at){
                                    return moment(data.updated_at).locale('id').format('DD MMMM YYYY HH:mm:ss');
                                }
                                return '-';
                            }
                        },
                        {
                            data: null,
                            render: function ( data, type, row, meta ) {
                                return '<a class="edit ml10 update-btn" href="javascript:void(0)" title="Edit" data-toggle="modal" data-target="#editModal" data-index="' + row.id + '">'+
                                    '<i class="glyphicon glyphicon-edit"></i>'+
                                    '</a>';
                            }
                        }
                    ]
                });

                $('#' + id).on('click','.update-btn',function(){
                    var index = $(this).data('index');
                    for(var i in data){
                        if(data[i].id==index){
                            $('#price').val(data[i].price);
                            $('#input_id').val(data[i].id);
                        }
                    }
                });
            }
        });
    </script>
@endsection