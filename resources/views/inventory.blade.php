@extends('layouts.master')

@section('title')
Persediaan Galon
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
            </header>
            <table class="table table-hover" id="gallon_inventory">
                <thead>
                <th>No</th>
                <th>Nama</th>
                <th>Jumlah (Gallon)</th>
                <th>Harga (Rupiah)</th>
                {{--<th align="center">Tgl Pembuatan</th>--}}
                <th align="center">Tgl Update</th>
                <th>Action</th>
                </thead>
            </table>
        </div>
    </div>

    <!-- Edit Modal -->

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('inventory.do.update')}}" method="POST">
                {{csrf_field()}}
                <input type="hidden" name="id" value="" id="input_id">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="editModalLabel">Edit Data</h4>
                </div>

                <div class="modal-body">                                              
                    <div class="form-group">
                        <label for="quantity"><strong>Jumlah Galon</strong></label>
                        <input id="quantity" type="number" class="form-control" name="quantity">
                    </div>          
                     <div class="form-group">
                        <label for="price"><strong>Harga (Rupiah)</strong></label>
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

            var inventories = [];
            $('#gallon_inventory').on('click','.detail-btn',function(){
                var index = $(this).data('index');
                for(var i in inventories){
                    if(inventories[i].id==index){                                     
                        $('#quantity').val(inventories[i].quantity);
                        $('#price').val(inventories[i].price);       
                        $('#input_id').val(inventories[i].id);
                    }
                }
            });

            $('#gallon_inventory').dataTable({
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
                ajax: {
                    url: '/getInventories',
                    dataSrc: ''
                },
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'quantity'},
                    {data: 'price',
                    render: function (data) {
                        return numeral(data).format('$0,0.00');
                    }},
//                    {data: null,
//                        render: function (data) {
//                            if(data.created_at){
//                                return moment(data.created_at).locale('id').format('DD MMMM YYYY HH:mm:ss');
//                            }
//                            return '-';
//                        }
//                    },
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
                            inventories.push({
                                'id': row.id,
                                'quantity': row.quantity,
                                'price': row.price                               
                            });
                            return '<a class="edit ml10 detail-btn" href="javascript:void(0)" title="Edit" data-toggle="modal" data-target="#editModal" data-index="' + row.id + '">'+
                                    '<i class="glyphicon glyphicon-edit"></i>'+
                                '</a>';
                        }
                    }                   
                ]
            });
        });
    </script>
@endsection