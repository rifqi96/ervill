@extends('layouts.master')

@section('title')
    Daftar Faktur Retur
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <h2>Faktur REFUND</h2>
            </header>
            <table class="table table-hover" id="refund_returns">
                <thead>
                <th>No Faktur</th>
                <th>Status</th>
                <th>Nama Customer</th>
                <th>Tgl Pembuatan</th>
                <th>Tgl Pengiriman</th>
                <th>Tgl Update</th>
                <th>Aksi</th>
                </thead>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <h2>Faktur NON REFUND</h2>
            </header>
            <table class="table table-hover" id="non_refund_returns">
                <thead>
                <th>No Faktur</th>
                <th>Status</th>
                <th>Nama Customer</th>
                <th>Tgl Pembuatan</th>
                <th>Tgl Pengiriman</th>
                <th>Tgl Update</th>
                <th>Aksi</th>
                </thead>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {

            var refund_returns = {!! $refund_returns->toJson() !!};
            var non_refund_returns = {!! $non_refund_returns->toJson() !!};

            for(var i=0; i<2; i++){
                var id = "";
                var name = "";
                var data = null;
                if(i==0){
                    id = "refund_returns";
                    name = "refund_returns";
                    data = refund_returns;
                }
                else{
                    id = "non_refund_returns";
                    name = "non_refund_returns";
                    data = non_refund_returns;
                }

                $('#' + id).dataTable({
                    fixedHeader: {
                        headerOffset: $('.site-header').outerHeight()
                    },
                    processing: true,
                    'order':[0, 'desc'],
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
                        {data: 'status',
                        render: function (data) {
                            if(data == "Selesai"){
                                return '<span class="label label-success">Selesai</span>';
                            }
                            else if(data == "Proses"){
                                return '<span class="label label-warning">Proses</span>';
                            }
                            else if(data == "Batal"){
                                return '<span class="label label-danger">Batal</span>';
                            }

                            return '<span class="label label-info">Draft</span>';
                        }},
                        {data: null,
                        render: function (data) {
                            if(data.has_order){
                                if(data.customer_id){
                                    return '<a href="/setting/customers/id/'+data.customer_id+'" target="_blank">'+data.customer_name+'</a>';
                                }
                            }

                            return '<i>Data tidak ditemukan</i>';
                        }},
                        {data: null,
                            render: function (data) {
                                if(data.created_at){
                                    return moment(data.created_at).locale('id').format('DD MMMM YYYY HH:mm:ss');
                                }
                                return '-';
                            }
                        },
                        {data: null,
                            render: function (data) {
                                if(data.delivery_at){
                                    return moment(data.delivery_at).locale('id').format('DD MMMM YYYY');
                                }
                                return '-';
                            }
                        },
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
                                return '<a href="return/id/'+row.id+'" target="_blank"><button class="btn btn-sm" type="button">Lihat</button></a>';
                            }
                        }
                    ]
                });
            }
        });
    </script>
@endsection