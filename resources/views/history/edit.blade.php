@extends('layouts.master')

@section('title')
    Edit History
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <table class="table table-hover" id="history_edit">
                <thead>
                <th>No</th>
                <th>Nama Modul</th>
                <th>Data ID</th>
                <th>Author ID</th>
                <th>Author Name</th>
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

            var edit_history_json = {!!$edit_history->toJson()!!};
            var old_values_template = "";
            var new_values_template = "";

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

            $('#history_edit').dataTable({
                fixedHeader: {
                    headerOffset: $('.site-header').outerHeight()
                },
                processing: true,
                'order':[5, 'desc'],
                data:edit_history_json,
                columns: [
                    {data: 'id'},
                    {data: 'module_name'},    
                    {data: 'data_id'},
                    {data: 'user_id'},  
                    {data: 'user.full_name'},
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
        });
    </script>

@endsection