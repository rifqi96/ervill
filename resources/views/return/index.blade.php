@extends('layouts.master')

@section('title')
    List Retur Order Customer
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <!--<h3 class="panel-title">Pesanan Air</h3>-->
                <a href="{{route('return.make')}}"><button class="btn btn-success">Lakukan Retur</button></a>
            </header>

            <table class="table table-hover" id="return">
                <thead>
                <th>Status</th>
                <th>No</th>
                <th>No Faktur</th>
                <th>Jenis</th>
                <th>Nama Customer</th>
                <th>No. Telepon</th>
                <th>Alamat Customer</th>
                <th>Jumlah Galon Isi</th>
                <th>Jumlah Galon Kosong</th>
                <th>Alasan</th>
                <th>Tgl Retur</th>
                <th>Tgl Pembuatan</th>
                <th>Admin</th>
                <th>Aksi</th>
                </thead>
            </table>
        </div>
    </div>

    <!-- Delete Modal -->

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="{{route('return.do.cancel')}}" method="POST">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="deleteModalLabel">Batalkan Retur</h4>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="description"><strong>Konfirmasi customer membatalkan retur galon dan galon sudah dikembalikan ke customer</strong></label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        {{csrf_field()}}
                        <input type="hidden" name="id" value="" id="delete-id">
                        <button type="submit" class="btn btn-danger">Batalkan</button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm Modal -->

    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="confirmModalLabel">Konfirmasi Retur</h4>
                </div>
                <form action="{{route('return.do.confirm')}}" method="POST">
                    {{csrf_field()}}
                    <input type="hidden" name="id" value="" id="confirm-id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="name"><strong>Pastikan barang retur sudah diterima</strong></label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Konfirmasi</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <script>
        $(document).ready(function () {
            $('#return').dataTable({
                order:[1, 'desc'],
                fixedHeader: {
                    headerOffset: $('.site-header').outerHeight()
                },
                processing: true,
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
                ajax: {
                    url: '/getReturns',
                    dataSrc: ''
                },
                columns: [
                    {data: null,
                        render: function(data, type, row, meta){
                            if(data.status == "Selesai"){
                                return '<span class="label label-success">Selesai</span>';
                            }
                            else if(data.status == "Proses"){
                                return '<span class="label label-warning">Proses</span>';
                            }
                            else if(data.status == "Batal"){
                                return '<span class="label label-danger">Batal</span>';
                            }
                            else{
                                return '<span class="label label-info">Draft</span>';
                            }
                        }},
                    {data:'id'},
                    {data: 'order_customer_return_invoices',
                        render: function (data) {
                            if(data.length>0){
                                return data[0].re_header_invoice_id;
                            }
                            return '-';
                        }
                    },
                    {data: 'order_customer_return_invoices',
                        render: function (data) {
                            if(data.length>0){
                                return data[0].re_header_invoice.payment_status;
                            }
                            return '-';
                        }
                    },
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
                    {data:'filled_gallon_quantity'},
                    {data:'empty_gallon_quantity'},
                    {data: null,
                        render: function(data){
                            if(data.description){
                                return data.description;
                            }
                            else{
                                return '-';
                            }
                        }},
                    {data: null,
                        render: function(data){
                            if(data.return_at){
                                return moment(data.return_at).locale('id').format('DD/MM/YYYY');
                            }
                            return '-';
                        }},
                    {data: null,
                        render: function(data){
                            if(data.updated_at){
                                return moment(data.updated_at).locale('id').format('DD/MM/YYYY HH:mm:ss');
                            }
                            return '-';
                        }},
                    {data: null,
                        render: function(data){
                            if(data.author.full_name){
                                return data.author.full_name;
                            }
                            return '<i>Data admin tidak ditemukan</i>';
                        }},
                    {data: null,
                        render: function(data, type, row, meta){
                            if(data.status != 'Selesai'){
                                return '<button class="btn btn-sm btn-success confirm-btn" type="button" data-toggle="modal" data-target="#confirmModal" data-index="' + data.id + '">Konfirmasi Retur</button>';
                            }

                            return '<button type="button" class="btn btn-sm btn-danger delete-modal" data-toggle="modal" data-target="#deleteModal" data-index="'+data.id+'">Batalkan Retur</button>';
                        }
                    },
                ]
            });

            $('#return').on('click', '.confirm-btn', function () {
                $('#confirm-id').val($(this).data('index'));
            });

            $('#return').on('click', '.delete-modal', function () {
                $('#delete-id').val($(this).data('index'));
            })
        });
    </script>

@endsection