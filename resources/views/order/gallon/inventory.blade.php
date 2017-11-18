@extends('layouts.master')

@section('title')
Inventory Gallon
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                {{--<h3 class="panel-title"></h3>--}}
                <a href="{{route('order.gallon.index')}}"><button class="btn btn-primary">Lihat Pesanan Gallon</button></a>
                <a href="{{route('order.gallon.inventory')}}"><button class="btn btn-primary">Pesan Galon</button></a>
            </header>
            <table class="table table-hover" id="gallon_inventory">
                <thead>
                <th>ID</th>
                <th>Nama</th>
                <th>Jumlah (Gallon)</th>
                <th>Harga (Rupiah)</th>
                <th align="center">Tgl Pembuatan</th>
                <th align="center">Tgl Update</th>
                <th>Action</th>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>Gallon Kosong</td>
                    <td>200</td>
                    <td>42.000,-</td>
                    <td>10/10/2017 08:20:55</td>
                    <td>15/10/2017 10:20:55</td>
                    <td>                        
                        <a class="edit ml10" href="javascript:void(0)" title="Edit" data-toggle="modal" data-target="#editModal">
                            <i class="glyphicon glyphicon-edit"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Gallon Isi</td>
                    <td>1000</td>
                    <td>19.000,-</td>
                    <td>10/10/2017 08:20:55</td>
                    <td>15/10/2017 10:20:55</td>
                    <td>                       
                        <a class="edit ml10" href="javascript:void(0)" title="Edit" data-toggle="modal" data-target="#editModal">
                            <i class="glyphicon glyphicon-edit"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Gallon Rusak</td>
                    <td>50</td>
                    <td>32.000,-</td>
                    <td>10/10/2017 08:20:55</td>
                    <td>15/10/2017 10:20:55</td>
                    <td>
                        <a class="edit ml10" href="javascript:void(0)" title="Edit" data-toggle="modal" data-target="#editModal">
                            <i class="glyphicon glyphicon-edit"></i>
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Edit Modal -->

    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="editModalLabel">Edit Data</h4>
                </div>

                <div class="modal-body">                       
                    <div class="form-group">
                        <label for="name"><strong>Nama</strong></label>
                        <input type="text" class="form-control" name="name">
                    </div>                                  
                    <div class="form-group">
                        <label for="quantity"><strong>Jumlah Galon</strong></label>
                        <input type="text" class="form-control" name="quantity">
                    </div>          
                     <div class="form-group">
                        <label for="price"><strong>Harga (Rupiah)</strong></label>
                        <input type="text" class="form-control" name="price">
                    </div>    
                    <div class="form-group">
                        <label for="description"><strong>Deskripsi Pengubahan Data</strong></label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
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
            $('#gallon_inventory').dataTable({
                scrollX: true,  
                fixedHeader: true,       
                processing: true,
                'order':[4, 'desc'],
                ajax: {
                    url: '/getInventories',
                    dataSrc: ''
                },
                columns: [
                    {data: 'id'},
                    {data: 'role.name'},
                    {data: 'username'},
                    {data: 'full_name'},
                    {data: 'email'},
                    {data: 'phone'},
                    {data: 'created_at'},
                    {data: 'updated_at'},
                    {
                        data: null, 
                        render: function ( data, type, row, meta ) {
                            if((<?php echo strcmp(auth()->user()->role->name,'admin');?> == 0 && row.role.name != 'driver') || 
                                (<?php echo strcmp(auth()->user()->role->name,'driver');?> == 0)
                                ){
                                return '';
                            }else{
                                users.push({
                                    'id': row.id,
                                    'role_id': row.role.id,
                                    'username': row.username,
                                    'full_name': row.full_name,
                                    'email': row.email,
                                    'phone': row.phone
                                });
                                return '<button class="btn btn-sm detail-btn" type="button" data-toggle="modal" data-target="#editModal" data-index="' + row.id + '">Edit</button>'+
                                    '<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal">Delete</button>';
                            }
                           
                        }
                    }                   
                ]
            });
        });
    </script>
@endsection