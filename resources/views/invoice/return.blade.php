@extends('layouts.master')

@section('title')
    Daftar Faktur Retur
@endsection

@section('content')
    <section class="box-typical box-typical-padding">
        <div class="row">
            <div class="col-xl-2 dashboard-column">
                <button type="button" data-index="non-refund" class="btn btn-primary">
                    Non Refund
                </button>
            </div>
            <div class="col-xl-2 dashboard-column">
                <button type="button" data-index="refund" class="btn btn-primary">
                    Refund
                </button>
            </div>
        </div>
    </section>

    <br>

    <div class="row">
        <div class="col-xl-12">
            <div class="tab-content">
                <div id="refund">
                    <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                        <h2>Faktur REFUND</h2>
                    </header>
                    <table class="table table-hover" id="refund_returns">
                        <thead>
                        <th>Status</th>
                        <th>No Faktur</th>
                        <th>Nama Customer</th>
                        <th>Tgl Retur</th>
                        <th>Tgl Pembuatan</th>
                        <th>Tgl Update</th>
                        <th>Aksi</th>
                        </thead>
                    </table>
                </div>
                <div id="non-refund">
                    <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                        <h2>Faktur NON REFUND</h2>
                    </header>
                    <table class="table table-hover" id="non_refund_returns">
                        <thead>
                        <th>Status</th>
                        <th>No Faktur</th>
                        <th>Nama Customer</th>
                        <th>Tgl Retur</th>
                        <th>Tgl Pembuatan</th>
                        <th>Tgl Update</th>
                        <th>Aksi</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

            $("#refund").hide();
            $("#non-refund").hide();
            $("#free").hide();

            var hash = window.location.hash;
            var id = hash.substring(hash.lastIndexOf('#') + 1);

            if(id){
                if(id == "refund" || id == "refund_invoices"){
                    $('button[data-index=refund]').attr('disabled', true);
                    $("#refund").show();
                }
                else if(id == "non_refund" || id == "non_refund_invoices"){
                    $('button[data-index=non-refund]').attr('disabled', true);
                    $("#non-refund").show();
                }
            }
            else{
                $('button[data-index=non-refund]').attr('disabled', true);
                $("#non-refund").show();
            }

            $('button[data-index=non-refund]').click(function () {
                if($(this).is(':not(:disabled)')){
                    $('button[data-index=non-refund]').attr('disabled', true);
                    $('button[data-index=refund]').attr('disabled', false);
                    $("#non-refund").fadeIn();
                    $("#refund").hide();
                }
            });

            $('button[data-index=refund]').click(function () {
                if($(this).is(':not(:disabled)')){
                    $('button[data-index=refund]').attr('disabled', true);
                    $('button[data-index=non-refund]').attr('disabled', false);
                    $("#refund").fadeIn();
                    $("#non-refund").hide();
                }
            });

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
                    'order':[1, 'desc'],
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
                            else if(data == "Bermasalah"){
                                return '<span class="label label-danger">Bermasalah</span>';
                            }
                            else if(data == "Dihapus"){
                                return '<span class="label label-danger">Dihapus</span>';
                            }

                            return '<span class="label label-info">Draft</span>';
                        }},
                        {data: 'id'},
                        {data: null,
                        render: function (data) {
                            if(data.customer_id){
                                return '<a href="/setting/customers/id/'+data.customer_id+'" target="_blank">'+data.customer_name+'</a>';
                            }

                            return '<i>Data tidak ditemukan</i>';
                        }},
                        {data: null,
                            render: function (data) {
                                if(data.delivery_at){
                                    return moment(data.delivery_at).locale('id').format('DD/MM/YYYY');
                                }
                                return '-';
                            }
                        },
                        {data: null,
                            render: function (data) {
                                if(data.created_at){
                                    return moment(data.created_at).locale('id').format('DD/MM/YYYY HH:mm:ss');
                                }
                                return '-';
                            }
                        },
                        {data: null,
                            render: function (data) {
                                if(data.updated_at){
                                    return moment(data.updated_at).locale('id').format('DD/MM/YYYY HH:mm:ss');
                                }
                                return '-';
                            }
                        },
                        {
                            data: null,
                            render: function ( data, type, row, meta ) {
                                var remove = '';
                                if(data.shipment_id && data.status == "Draft"){
                                    remove = '<a href="/invoice/return/do/remove/shipment/'+data.id+'"><button class="btn btn-sm btn-danger" type="button">Hapus pengiriman</button></a>';
                                }

                                return '<a href="return/id/'+row.id+'" target="_blank"><button class="btn btn-sm" type="button">Lihat</button></a>' +
                                    remove;
                            }
                        }
                    ]
                });
            }
        });
    </script>
@endsection