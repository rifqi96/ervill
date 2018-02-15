@extends('layouts.master')

@section('title')
    Daftar Faktur Penjualan
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <h2>Faktur Lunas</h2>
            </header>
            <table class="table table-hover" id="cash_invoices">
                <thead>
                <th>Status</th>
                <th>No Faktur</th>
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
                <h2>Faktur Piutang</h2>
            </header>
            <table class="table table-hover" id="piutang_invoices">
                <thead>
                <th>Status</th>
                <th>No Faktur</th>
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
                <h2>Faktur Barang Free / Sample</h2>
            </header>
            <table class="table table-hover" id="free_invoices">
                <thead>
                <th>Status</th>
                <th>No Faktur</th>
                <th>Nama Customer</th>
                <th>Tgl Pembuatan</th>
                <th>Tgl Pengiriman</th>
                <th>Tgl Update</th>
                <th>Aksi</th>
                </thead>
            </table>
        </div>
    </div>

    <!-- Edit Modal -->

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{route('invoice.sales.do.pay')}}" method="POST">
                    {{csrf_field()}}
                    <input type="hidden" name="id" value="" id="input_id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="editModalLabel">Konfirmasi Pelunasan Piutang</h4>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label><strong>Konfirmasi pelunasan piutang dari customer: Per tanggal hari ini, jenis faktur ini akan diubah menjadi faktur penjualan lunas / cash</strong></label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">LUNAS</button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                    </div>
                </form>


            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

            var cash_invoices = {!! $cash_invoices->toJson() !!};
            var piutang_invoices = {!! $piutang_invoices->toJson() !!};
            var free_invoices = {!! $free_invoices->toJson() !!};

            for(var i=0; i<3; i++){
                var id = "";
                var name = "";
                var data = null;
                if(i==0){
                    id = "cash_invoices";
                    name = "cash_invoices";
                    data = cash_invoices;
                }
                else if(i==1){
                    id = "piutang_invoices";
                    name = "piutang_invoices";
                    data = piutang_invoices;
                }
                else{
                    id = "free_invoices";
                    name = "free_invoices";
                    data = free_invoices;
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
                        {data: 'customer',
                        render: function (data) {
                            if(data){
                                return '<a href="/setting/customers/id/'+data.id+'" target="_blank">'+data.name+'</a>';
                            }

                            return '<i>Data tidak ditemukan</i>';
                        }},
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
                                if(data.delivery_at){
                                    return moment(data.delivery_at).locale('id').format('DD/MM/YYYY');
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
                                    remove = '<a href="/invoice/sales/do/remove/shipment/'+data.id+'"><button class="btn btn-sm btn-danger" type="button">Hapus pengiriman</button></a>';
                                }

                                if(data.payment_status == 'piutang'){
                                    return '<a href="sales/id/'+row.id+'" target="_blank"><button class="btn btn-sm" type="button">Lihat</button></a>' +
                                        '<a href="sales/wh/id/'+row.id+'" target="_blank"><button class="btn btn-sm" type="button">Logistik Gudang</button></a>' +
                                        remove +
                                        '<button class="btn btn-sm btn-success pay-btn" type="button" data-toggle="modal" data-target="#editModal" data-index="' + row.id + '">Lunas</button>';
                                }

                                return '<a href="sales/id/'+row.id+'" target="_blank"><button class="btn btn-sm" type="button">Lihat</button></a>' +
                                    '<a href="sales/wh/id/'+row.id+'" target="_blank"><button class="btn btn-sm" type="button">Logistik Gudang</button></a>' +
                                    remove;
                            }
                        }
                    ]
                });
            }

            $('#piutang_invoices').on('click','.pay-btn',function(){
                var index = $(this).data('index');
                for(var i in piutang_invoices){
                    if(piutang_invoices[i].id==index){
                        $('#input_id').val(piutang_invoices[i].id);
                    }
                }
            });
        });
    </script>
@endsection