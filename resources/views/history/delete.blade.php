@extends('layouts.master')

@section('title')
    Delete History
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <div class="row" style="margin-bottom:3vh;">
                <div class="col-xl-12">
                    <form action="{{route('history.do.mass_restore_or_delete')}}" method="POST">
                        {{csrf_field()}}
                        <div id="ids-list"></div>
                        <input type="hidden" name="delete_id" value="">
                        <input type="hidden" name="submit_btn" value="">
                        <button type="submit" class="btn btn-danger force_delete" id="mass-delete">Hapus Data Permanen</button>
                        <button type="submit" class="btn btn-success restore" id="mass-restore">Kembalikan Data</button>
                        {{--<button type="button" class="btn btn-secondary showFilterBy">Kolom Pencarian</button>--}}
                    </form>
                </div>
            </div>

            <div class="row filterBy">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Kolom pencarian</h5>
                        </div>
                        <div class="card-block">
                            <form id="filterBy">
                                <div class="row form-group">
                                    <div class="col-xl-3">Nama Modul:</div>
                                    <div class="col-xl-9">
                                        <select name="module_name[]" id="search-module" class="form-control select2" multiple="multiple">
                                            <option value="">-- Silahkan Pilih --</option>
                                            <option value="Order Customer">Order Customer</option>
                                            <option value="Shipment">Pengiriman</option>
                                            <option value="Inventory">Inventori</option>
                                            <option value="Order Gallon">Order Gallon</option>
                                            <option value="Order Water">Order Air</option>
                                            <option value="User Management">User</option>
                                            <option value="Outsourcing Driver">Outsourcing Driver</option>
                                            <option value="Customers">Customer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-xl-3">No. Transaksi / Data ID:</div>
                                    <div class="col-xl-9">
                                        <select name="data_id[]" id="search-id" class="form-control select2" multiple="multiple">
                                            <option value="">-- Silahkan Pilih --</option>
                                            @foreach($datas as $data)
                                                <option value="{{$data->data_id}}">{{$data->data_id}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-xl-3">Nama Admin:</div>
                                    <div class="col-xl-9">
                                        <select name="user_fullname[]" id="search-fullname" class="form-control select2" multiple="multiple">
                                            <option value="">-- Silahkan Pilih --</option>
                                            @foreach($users as $user)
                                                <option value="{{$user->full_name}}">{{$user->full_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-xl-3">Tanggal Delete:</div>
                                    <div class="col-xl-9">
                                        <input type="date" name="created_at" class="form-control" id="search-date" value="">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-xl-3"></div>
                                    <div class="col-xl-9">
                                        {{csrf_field()}}
                                        <button type="submit" class="btn btn-primary search-btn">Cari</button>
                                        <button type="reset" class="btn btn-info reset-btn">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <table class="table table-hover" id="deleteTable">
                        <thead>
                        <th><input type="checkbox" class="checkbox select-all"></th>
                        <th>No</th>
                        <th>Nama Modul</th>
                        <th>Data ID</th>
                        <th>Admin</th>
                        <th>Tgl Delete</th>
                        <th>Action</th>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>


    <!-- Detail Modal -->

    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModallLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="detailModallLabel">Detail Data</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="module_name"><strong>Module Name</strong></label>
                        <p id="module_name" class="form-control-static">
                        </p>
                    </div>

                    <div id="values_template"></div>

                    <hr>

                    <div class="form-group">
                        <label for="description"><strong>Alasan Menghapus Data</strong></label>
                        <p id="description" class="form-control-static"></p>
                    </div>
                </div>
                <form method="POST" action="{{route('history.do.restore_or_delete')}}">
                    <div class="modal-footer">
                        {{csrf_field()}}
                        <input type="hidden" name="delete_id" id="delete-id-input">
                        <input type="hidden" name="submit_btn" value="">
                        <button type="submit" class="btn btn-danger force_delete">Hapus Data Permanen</button>
                        <button type="submit" class="btn btn-success restore">Kembalikan Data</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <script>
        $(document).ready(function () {
            $('#ids-list').hide();
            $('#mass-restore').attr('disabled', true);
            $('#mass-delete').attr('disabled', true);

//            $('.filterBy').hide();
//
//            $('.showFilterBy').click(function () {
//                $('.filterBy').slideToggle();
//            });

            $('#filterBy').submit(function (e) {
                e.preventDefault();

                $.post("{{route('history.delete.filterby')}}", $(this).serialize())
                    .done(function (result) {
                        console.log('success');
                        console.log(result);
                        delete_table(result);
                    })
                    .fail(function (msg) {
                        console.log('error');
                        console.log(msg);
                    });
                $(this).find('button[type=submit]').prop('disabled', false);
            });

            $('#filterBy .reset-btn').click(function () {
                $('#search-id').val('');
                $('#search-id').trigger('change');
                $('#search-fullname').val('');
                $('#search-fullname').trigger('change');
                $('#search-module').val('');
                $('#search-module').trigger('change');
            });

            delete_table({!! $delete_histories->toJson() !!});

        });

        var changeSelect = function(elem){
            if(elem.checked){
                var index = $(elem).val();

                $(elem).attr('checked', true);
                $('#ids-list').append($('<input>', {
                    name: 'ids[]',
                    value: $(elem).val(),
                    "data-index": index
                }));
            }
            else{
                var index = $(elem).val();
                $(elem).removeAttr('checked');
                $('#ids-list input[data-index='+index+']').remove();
            }

            if($('.ids:checked').length > 0){
                $('#mass-restore').attr('disabled', false);
                $('#mass-delete').attr('disabled', false);
            }
            else{
                $('#mass-restore').attr('disabled', true);
                $('#mass-delete').attr('disabled', true);
            }
        };

        var delete_table = function (delete_histories_json) {
            var values_template = "";

            $('#deleteTable').on('change', '.ids', function () {
                changeSelect(this);
            });

            $('.select-all').on('change', function () {
                if(this.checked){
                    $('#deleteTable').find('.ids').each(function(){
                        $(this).prop('checked', true);
                        changeSelect(this);
                    });
                }
                else{
                    $('#deleteTable').find('.ids').each(function(){
                        $(this).prop('checked', false);
                        changeSelect(this);
                    });
                }
            });

            $('#deleteTable').on('click', '.detail-btn', function () {
                $('#values_template').empty();

                for(var i in delete_histories_json){
                    if(delete_histories_json[i].id == $(this).data('index')){
                        $('#module_name').text(delete_histories_json[i].module_name);
                        $('#description').text(delete_histories_json[i].description);

                        for(var j in delete_histories_json[i].data_id){
                            values_template +=
                                '<div class="form-group row">' +
                                '<label class="col-sm-5"><strong>'+j+'</strong></label>' +
                                '<div class="col-sm-7">' +
                                '<p class="form-control-static">' +
                                delete_histories_json[i].data_id[j] +
                                '</p>' +
                                '</div>' +
                                '</div>';
                        }

                        $('#values_template').append(values_template);
                        $('#delete-id-input').val(delete_histories_json[i].id);

                        values_template = "";
                    }
                }
            });

            $('#deleteTable').DataTable().destroy();
            $('#deleteTable').dataTable({
                fixedHeader: {
                    headerOffset: $('.site-header').outerHeight()
                },
                processing: true,
                'order':[1, 'desc'],
                data:delete_histories_json,
                columns: [
                    {
                        data: null,
                        render: function (data, type, row, meta) {
                            return '<input type="checkbox" class="checkbox ids" name="ids[]" value="' + row.id + '">';
                        }
                    },
                    {data: 'id'},
                    {data: 'module_name'},
                    {
                        data: 'data_id',
                        render: 'id'
                    },
                    {
                        data: null,
                        render: function (data) {
                            if(data.user){
                                return '<a href="/setting/user_management/id/'+data.user.id+'" target="_blank" title="Klik untuk lihat">'+data.user.full_name+'</a>';
                            }
                            return 'User tidak ditemukan';
                        }
                    },
                    {data: null,
                        render: function (data) {
                            if(data.created_at){
                                return moment(data.created_at).locale('id').format('DD MMMM YYYY HH:mm:ss');
                            }
                            return '-';
                        }
                    },
                    {
                        data: null,
                        render: function ( data, type, row, meta ) {

                            return '<button class="btn btn-sm btn-warning detail-btn" type="button" data-toggle="modal" data-target="#detailModal" data-index="' + row.id + '">Detail</button>';
                        }
                    }
                ]
            });

            $('.force_delete').on('click', function () {
                $(this).siblings('input[name=submit_btn]').val('force_delete');
            });

            $('.restore').on('click', function () {
                $(this).siblings('input[name=submit_btn]').val('restore');
            });
        };
    </script>

@endsection