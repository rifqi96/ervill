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
                        <input type="hidden" name="delete_id" val="">
                        <button type="submit" name="submit_btn" value="force_delete" class="btn btn-danger" id="mass-delete">Hapus Data Permanen</button>
                        <button type="submit" name="submit_btn" value="restore" class="btn btn-success" id="mass-restore">Kembalikan Data</button>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <table class="table table-hover" id="deleteTable">
                        <thead>
                        <th><input type="checkbox" class="checkbox select-all"></th>
                        <th>ID</th>
                        <th>Nama Modul</th>
                        <th>Data ID</th>
                        <th>Author ID</th>
                        <th>Author Name</th>
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
                        <button type="submit" name="submit_btn" value="force_delete" class="btn btn-danger">Hapus Data Permanen</button>
                        <button type="submit" name="submit_btn" value="restore" class="btn btn-success">Kembalikan Data</button>
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

            var delete_histories_json = {!! $delete_histories->toJson() !!};
            var values_template = "";
            var changeSelect = function(elem){
                if(elem.checked){
                    var index = 'id_' + $(elem).val();

                    $(elem).attr('checked', true);
                    $('#ids-list').append($('<input>', {
                        name: 'ids[]',
                        value: $(elem).val(),
                        "data-index": index
                    }));
                }
                else{
                    $(elem).removeAttr('checked');
                    $('#ids-list').find('input').attr('data-index',index).remove();
                }

                if(typeof $('.ids').attr('checked') !== typeof undefined && $('.ids').attr('checked').length > 0){
                    $('#mass-restore').attr('disabled', false);
                    $('#mass-delete').attr('disabled', false);
                }
                else{
                    $('#mass-restore').attr('disabled', true);
                    $('#mass-delete').attr('disabled', true);
                }
            }

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
                                '<label class="col-sm-3"><strong>'+j+'</strong></label>' +
                                '<div class="col-sm-9">' +
                                '<p class="form-control-static">' +
                                delete_histories_json[i].data_id[j] +
                                '</p>' +
                                '</div>' +
                                '</div>';
                        }

                        $('#values_template').append(values_template);
                        $('#delete-id-input').val(delete_histories_json[i].id)

                        values_template = "";
                    }
                }
            });

            $('#deleteTable').dataTable({
                scrollX: true,
                fixedHeader: true,
                processing: true,
                'order':[5, 'desc'],
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
                        data: 'user',
                        render: 'id'
                    },
                    {
                        data: 'user',
                        render: 'full_name'
                    },
                    {data: 'created_at'},
                    {
                        data: null,
                        render: function ( data, type, row, meta ) {

                            return '<button class="btn btn-sm btn-warning detail-btn" type="button" data-toggle="modal" data-target="#detailModal" data-index="' + row.id + '">Detail</button>';
                        }
                    }
                ]
            });
        });
    </script>

@endsection