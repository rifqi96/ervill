@extends('layouts.master')

@section('title')
    Daftar Faktur Penjualan
@endsection

@section('content')
    <section class="box-typical box-typical-padding">
        <div class="row">
            <div class="col-xl-2 dashboard-column">
                <button type="button" data-index="lunas" class="btn btn-primary">
                    Lunas
                </button>
            </div>
            <div class="col-xl-2 dashboard-column">
                <button type="button" data-index="piutang" class="btn btn-primary">
                    Piutang
                </button>
            </div>
            <div class="col-xl-2 dashboard-column">
                <button type="button" data-index="free" class="btn btn-primary">
                    Gratis/Free Sample
                </button>
            </div>
        </div>
    </section>

    <br>

    <div class="row">
        <div class="col-xl-12">
            <div class="tab-content">
                <div id="lunas">
                    <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                        <h2>Faktur LUNAS</h2>
                    </header>
                    <table class="table table-hover" id="cash_invoices">
                        <thead>
                        <th>Status</th>
                        <th>No Faktur</th>
                        <th>Nama Customer</th>
                        <th>Total</th>
                        <th>Tgl Penjualan</th>
                        <th>Tgl Pembuatan</th>
                        <th>Tgl Update</th>
                        <th>Aksi</th>
                        </thead>
                    </table>
                </div>
                <div id="piutang">
                    <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                        <h2>Faktur PIUTANG</h2>
                    </header>
                    <table class="table table-hover" id="piutang_invoices">
                        <thead>
                        <th>Status</th>
                        <th>No Faktur</th>
                        <th>Nama Customer</th>
                        <th>Total</th>
                        <th>Tgl Penjualan</th>
                        <th>Tgl Pembuatan</th>
                        <th>Tgl Update</th>
                        <th>Aksi</th>
                        </thead>
                    </table>
                </div>
                <div id="free">
                    <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                        <h2>Faktur Gratis/Free Sample</h2>
                    </header>
                    <table class="table table-hover" id="free_invoices">
                        <thead>
                        <th>Status</th>
                        <th>No Faktur</th>
                        <th>Nama Customer</th>
                        <th>Total</th>
                        <th>Tgl Penjualan</th>
                        <th>Tgl Pembuatan</th>
                        <th>Tgl Update</th>
                        <th>Aksi</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="edit-form" action="" method="POST">
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

            $("#piutang").hide();
            $("#lunas").hide();
            $("#free").hide();

            var hash = window.location.hash;
            var id = hash.substring(hash.lastIndexOf('#') + 1);

            if(id){
                if(id == "piutang" || id == "piutang_invoices"){
                    $('button[data-index=piutang]').attr('disabled', true);
                    $("#piutang").show();
                }
                else if(id == "lunas" || id == "lunas_invoices"){
                    $('button[data-index=lunas]').attr('disabled', true);
                    $("#lunas").show();
                }
                else if(id == "free" || id == "free_invoices"){
                    $('button[data-index=free]').attr('disabled', true);
                    $("#free").show();
                }
            }
            else{
                $('button[data-index=lunas]').attr('disabled', true);
                $("#lunas").show();
            }
            
            $('button[data-index=lunas]').click(function () {
                if($(this).is(':not(:disabled)')){
                    $('button[data-index=lunas]').attr('disabled', true);
                    $('button[data-index=free]').attr('disabled', false);
                    $('button[data-index=piutang]').attr('disabled', false);
                    $("#lunas").fadeIn();
                    $("#piutang").hide();
                    $("#free").hide();
                }
            });

            $('button[data-index=piutang]').click(function () {
                if($(this).is(':not(:disabled)')){
                    $('button[data-index=piutang]').attr('disabled', true);
                    $('button[data-index=lunas]').attr('disabled', false);
                    $('button[data-index=free]').attr('disabled', false);
                    $("#piutang").fadeIn();
                    $("#lunas").hide();
                    $("#free").hide();
                }
            });

            $('button[data-index=free]').click(function () {
                if($(this).is(':not(:disabled)')){
                    $('button[data-index=free]').attr('disabled', true);
                    $('button[data-index=lunas]').attr('disabled', false);
                    $('button[data-index=piutang]').attr('disabled', false);
                    $("#free").fadeIn();
                    $("#lunas").hide();
                    $("#piutang").hide();
                }
            });

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
                        {data: null,
                        render: function (data) {
                            if(data.customer){
                                if(data.invoice_code == "oc"){
                                    return '<a href="/setting/customers/id/'+data.customer.id+'" target="_blank">'+data.customer.name+'</a>';
                                }
                                else if(data.invoice_code == "ne"){
                                    return '<a href="/setting/customerNonErvills/id/'+data.customer.id+'" target="_blank">'+data.customer.name+'</a>';
                                }

                                return '<i>Data tidak ditemukan</i>';
                            }

                            return '<i>Data tidak ditemukan</i>';
                        }},
                        {data: 'total',
                        render: function (data) {
                            if(data){
                                return '<div class="numeral">'+data+'</div>';
                            }
                            return '<div class="numeral">0</div>';
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
                                    remove = '<a href="/invoice/sales/do/remove/shipment/'+data.id+'"><button class="btn btn-sm btn-danger" type="button">Hapus pengiriman</button></a>';
                                }

                                if(data.payment_status == 'piutang'){
                                    if(data.invoice_code == "oc"){
                                        return '<a href="sales/id/'+row.id+'" target="_blank"><button class="btn btn-sm" type="button">Lihat</button></a>' +
                                            '<a href="sales/wh/id/'+row.id+'" target="_blank"><button class="btn btn-sm" type="button">Logistik Gudang</button></a>' +
                                            remove +
                                            '<button class="btn btn-sm btn-success pay-btn" type="button" data-toggle="modal" data-target="#editModal" data-index="' + row.id + '" data-type="' + row.invoice_code + '">Lunas</button>';
                                    }
                                    else if(data.invoice_code == "ne"){
                                        return '<a href="salesNonErvill/id/'+row.id+'" target="_blank"><button class="btn btn-sm" type="button">Lihat</button></a>' +
                                            '<a href="salesNonErvill/wh/id/'+row.id+'" target="_blank"><button class="btn btn-sm" type="button">Logistik Gudang</button></a>' +
                                            remove +
                                            '<button class="btn btn-sm btn-success pay-btn" type="button" data-toggle="modal" data-target="#editModal" data-index="' + row.id + '" data-type="' + row.invoice_code + '">Lunas</button>';
                                    }
                                }

                                if(data.invoice_code == "oc"){
                                    return '<a href="sales/id/'+row.id+'" target="_blank"><button class="btn btn-sm" type="button">Lihat</button></a>' +
                                        '<a href="sales/wh/id/'+row.id+'" target="_blank"><button class="btn btn-sm" type="button">Logistik Gudang</button></a>' +
                                        remove;
                                }
                                else if(data.invoice_code == "ne"){
                                    return '<a href="salesNonErvill/id/'+row.id+'" target="_blank"><button class="btn btn-sm" type="button">Lihat</button></a>' +
                                        '<a href="salesNonErvill/wh/id/'+row.id+'" target="_blank"><button class="btn btn-sm" type="button">Logistik Gudang</button></a>' +
                                        remove;
                                }
                            }
                        }
                    ]
                });

                $('#' + id + ' .numeral').each(function () {
                    var price = $(this).text();
                    $(this).text(numeral(price).format('$0,0'));
                });

                $('#' + id + '_paginate').on('click', function () {
                    $('.numeral').each(function () {
                        var price = $(this).text();
                        $(this).text(numeral(price).format('$0,0'));
                    });
                });
            }

            $('#piutang_invoices').on('click','.pay-btn',function(){
                var index = $(this).data('index');
                var type = $(this).data('type');

                if(type == "oc"){
                    $('#edit-form').attr('action', '{{route('invoice.sales.do.pay')}}');
                }
                else if(type == "ne"){
                    $('#edit-form').attr('action', '{{route('invoice.salesNonErvill.do.pay')}}');
                }

                for(var i in piutang_invoices){
                    if(piutang_invoices[i].id==index){
                        $('#input_id').val(piutang_invoices[i].id);
                    }
                }
            });
        });
    </script>
@endsection