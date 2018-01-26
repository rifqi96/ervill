@extends('layouts.master')

@section('title')
    List Pindah Tangan Galon
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <!--<h3 class="panel-title">Pesanan Air</h3>-->
                <a href="{{route('order.customer.buy.make')}}"><button class="btn btn-success">Lakukan Transaksi</button></a>
                <a href="{{route('order.customer.index')}}"><button class="btn btn-primary">Kembali Ke List Pesanan Customer</button></a>
            </header>

            <table class="table table-hover" id="buy-gallon-order">
                <thead>
                <th>No</th>
                <th>No Struk</th>
                <th>Nama Customer</th>
                <th>No. Telepon</th>
                <th>Alamat Customer</th>
                <th>Jumlah Galon</th>
                <th>Tgl Order</th>
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
                <form action="{{route('order.customer.buy.do.delete')}}" method="POST">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="deleteModalLabel">Delete Data</h4>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="description"><strong>Konfirmasi batalkan pindah tangan galon</strong></label>
                        </div>
                    </div>

                    <div class="modal-footer">
                        {{csrf_field()}}
                        <input type="hidden" name="id" value="" id="delete-id">
                        <button type="submit" class="btn btn-danger">Delete</button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {

            $.ajax({
                url: '/getOrderCustomerBuys',
                type: 'GET',
                dataType: 'json',
                success: function(result){
                    $('#buy-gallon-order').dataTable({
                        order:[2, 'desc'],
                        fixedHeader: {
                            headerOffset: $('.site-header').outerHeight()
                        },
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
                        data:result,
                        columns: [
                            {data: 'id'},
                            {data: 'no_struk',
                            render: function (data) {
                                if(data || data !== ''){
                                    return data;
                                }
                                return '-';
                            }},
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
                            {data:'quantity'},
                            {data: null,
                                render: function (data) {
                                    if(data.buy_at){
                                        return moment(data.buy_at).locale('id').format('DD MMMM YYYY HH:mm:ss');
                                    }
                                    return '-';
                                }
                            },
                            {data: null,
                                render: function(data){
                                    if(data.author.full_name){
                                        return data.author.full_name;
                                    }
                                    return '<i>Data admin tidak ditemukan</i>';
                                }},
                            {data: null,
                                render: function(data, type, row, meta){
                                    return '<button type="button" class="btn btn-sm btn-danger delete-modal" data-toggle="modal" data-target="#deleteModal" data-index="'+data.id+'">Delete</button>'
                                }
                            },

                        ],
                        processing: true
                    });

                    $('#buy-gallon-order').on('click','.delete-modal', function(){
                        $('#delete-id').val($(this).data('index'));
                    });
                }
            });
        });
    </script>

@endsection