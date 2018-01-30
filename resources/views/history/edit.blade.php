@extends('layouts.master')

@section('title')
    Edit History
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <button class="btn btn-secondary showFilterBy">Kolom Pencarian</button>
            </header>

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
                                        {{--<input type="text" name="data_id" class="form-control" id="search-id" placeholder="101">--}}
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
                                        {{--<input type="text" name="user_fullname" class="form-control" id="search-fullname" placeholder="Budi">--}}
                                        <select name="user_fullname[]" id="search-fullname" class="form-control select2" multiple="multiple">
                                            <option value="">-- Silahkan Pilih --</option>
                                            @foreach($users as $user)
                                                <option value="{{$user->full_name}}">{{$user->full_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-xl-3">Tanggal Edit:</div>
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

            <table class="table table-hover" id="history_edit">
                <thead>
                <th>No</th>
                <th>Nama Modul</th>
                <th>Data ID</th>
                <th>Admin</th>
                <th>Tgl Edit</th>
                <th>Action</th>
                </thead>                
            </table>

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
                    <div class="form-group">
                        <label for="data_id"><strong>Data ID</strong></label>
                        <p id="data_id" class="form-control-static">
                           
                        </p> 
                    </div>

                    <h3 class="text-center">Data Lama</h3>
                    <div id="old_values"></div>
                     

                    <h3 class="text-center">Data Baru</h3>
                    <div id="new_values"></div>                    
                  
                    <hr>

					<div class="form-group">
						<label for="description"><strong>Alasan Pengubahan Data</strong></label>
						<p id="description" class="form-control-static"></p>
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

            $('.filterBy').hide();

            $('.showFilterBy').click(function () {
                $('.filterBy').slideToggle();
            });

            $('#filterBy').submit(function (e) {
                e.preventDefault();

                $.post("{{route('history.edit.filterby')}}", $(this).serialize())
                    .done(function (result) {
                        console.log('success');
                        console.log(result);
                        history_edit_table(result);
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

            history_edit_table({!!$edit_history->toJson()!!});
        });

        var history_edit_table = function (edit_history_json) {
            var old_values_template = "";
            var new_values_template = "";

            $('#history_edit').DataTable().destroy();
            $('#history_edit').dataTable({
                fixedHeader: {
                    headerOffset: $('.site-header').outerHeight()
                },
                processing: true,
                'order':[0, 'desc'],
                data:edit_history_json,
                columns: [
                    {data: 'id'},
                    {data: 'module_name'},
                    {data: 'data_id'},
                    {data: null,
                        render: function (data) {
                            if(data.user){
                                return '<a href="/setting/user_management/id/'+data.user.id+'" target="_blank" title="Klik untuk lihat">'+data.user.full_name+'</a>';
                            }
                            return 'User tidak ditemukan';
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

                            return '<button class="btn btn-sm btn-warning detail-btn" type="button" data-toggle="modal" data-target="#detailModal" data-index="' + row.id + '">Detail</button>';
                        }
                    }
                ]
            });

            $('#history_edit').on('click','.detail-btn',function(){
                $('#old_values').empty();
                $('#new_values').empty();

                for(var i in edit_history_json){
                    if(edit_history_json[i].id==$(this).data('index')){
                        $('#module_name').text(edit_history_json[i].module_name);
                        $('#data_id').text(edit_history_json[i].data_id);
                        $('#description').text(edit_history_json[i].description);

                        for(var j in edit_history_json[i].old_value){
                            old_values_template +=
                                '<div class="form-group row">' +
                                '<label class="col-sm-3"><strong>'+j+'</strong></label>' +
                                '<div class="col-sm-9">' +
                                '<p class="form-control-static">' +
                                edit_history_json[i].old_value[j] +
                                '</p>' +
                                '</div>' +
                                '</div>';
                        }

                        for(var j in edit_history_json[i].new_value){
                            new_values_template +=
                                '<div class="form-group row">' +
                                '<label class="col-sm-3"><strong>'+j+'</strong></label>' +
                                '<div class="col-sm-9">' +
                                '<p class="form-control-static">' +
                                edit_history_json[i].new_value[j] +
                                '</p>' +
                                '</div>' +
                                '</div>';
                        }

                        $('#old_values').append(old_values_template);
                        $('#new_values').append(new_values_template);

                        old_values_template = "";
                        new_values_template = "";
                    }
                }
            });
        };
    </script>

@endsection