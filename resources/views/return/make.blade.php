@extends('layouts.master')

@section('title')
    Pembuatan Retur Order
@endsection

@section('content')

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <section class="box-typical box-typical-padding">

                <form action="{{route('return.do.make')}}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group row customer-table-container">
                        <div class="col-sm-12">
                            <h4 class="box-typical-header"><label for="existingCustomerTable" class="form-control-label">Silahkan pilih customer</label></h4>
                            <table id="customer-table">
                                <thead>
                                <th></th>
                                <th>No</th>
                                <th>Nama Customer</th>
                                <th>Alamat</th>
                                <th>No. Telepon</th>
                                <th>Jenis</th>
                                <th>Aksi</th>
                                </thead>
                            </table>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Refund/Kembalikan Uang Customer?</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><input id="is_refund" type="checkbox" class="form-control" name="is_refund"></p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Jumlah Galon Kosong</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><input id="empty_quantity" type="number" class="form-control" name="empty_quantity" max="" min="0"></p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Jumlah Galon Isi</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><input id="filled_quantity" type="number" class="form-control" name="filled_quantity" max="" min="0"></p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Tgl Retur</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                                <input type="date" class="form-control" name="return_at" placeholder="Tgl Retur" value="{{\Carbon\Carbon::now()->toDateString()}}">
                            </p>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Alasan Retur</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">
                                <textarea name="description" id="description" class="form-control" cols="30" rows="10"></textarea>
                            </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-10">
                            <input type="submit" value="Submit" class="btn">
                            <input type="reset" value="Reset" class="btn btn-info">
                        </div>
                    </div>
                </form>
            </section><!--.box-typical-->
        </div>
    </div>

    <!-- Asset Modal -->

    <div class="modal fade" id="assetModal" tabindex="-1" role="dialog" aria-labelledby="assetModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">


                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="assetModalLabel">Aset Customer</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label><strong>Galon Pinjam</strong></label>
                        <p class="form-control-static" id="rent"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Galon Beli</strong></label>
                        <p class="form-control-static" id="purchase"></p>
                    </div>
                    <div class="form-group">
                        <label><strong>Galon Tukar Non-Ervill</strong></label>
                        <p class="form-control-static" id="non_ervill"></p>
                    </div>
                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                </div>



            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var customers = {!! $customers !!};

            $('#customer-table').dataTable({
                scrollX: true,
                fixedHeader: {
                    headerOffset: $('.site-header').outerHeight()
                },
                data:customers,
                columns: [
                    {data: null,
                        render: function (data, type, row, meta) {
                            return '<input class="radio customer-id" type="radio" name="customer_id" value="'+data.id+'">';
                        }},
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'address'},
                    {data: 'phone'},
                    {data: 'type',
                        render: function(data) {
                            if(data == "end_customer")
                                return "End Customer";
                            else if(data == "agent")
                                return "Agen";

                            return "-";
                        }
                    },
                    {data: null,
                        render: function (data, type, row, meta) {
                            return '<button class="btn btn-sm confirm-btn" type="button" data-toggle="modal" data-target="#assetModal" data-index="' + row.id + '">Lihat Aset</button>';
                        }},
                ],
                processing: true,
                'order':[1, 'desc']
            });

            $('#customer-table').on('click','.confirm-btn',function(){
                $('#rent').text('');
                $('#purchase').text('');
                $('#non_ervill').text('');
                for(var i in customers){
                    if(customers[i].id==$(this).data('index')){
                        var rent_qty = customers[i].rent_qty ? customers[i].rent_qty : 0;
                        var purchase_qty = customers[i].purchase_qty ? customers[i].purchase_qty : 0;
                        var non_erv_qty = customers[i].non_erv_qty ? customers[i].non_erv_qty : 0;
                        $('#rent').text(rent_qty);
                        $('#purchase').text(purchase_qty);
                        $('#non_ervill').text(non_erv_qty);
                    }
                }
            });
        });
    </script>

@endsection