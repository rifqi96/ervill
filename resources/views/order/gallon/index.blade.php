@extends('layouts.master')

@section('title')
List Pesanan Galon
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                {{--<h3 class="panel-title"></h3>--}}
                <a href="{{route('order.gallon.make')}}"><button class="btn btn-primary">Pesan Galon</button></a>
                <a href="{{route('order.gallon.inventory')}}"><button class="btn btn-primary">Stock Gudang</button></a>
            </header>
            <table class="table table-hover" id="gallon_order">
                <thead>
                <th>ID</th>
                <th>Admin</th>
                <th>Outsourcing</th>
                <th>Jumlah (Gallon)</th>
                <th align="center">Tgl Order</th>
                <th align="center">Tgl Penerimaan</th>
                <th>Actions</th>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>Ibu Dwi</td>
                    <td>PT Jingkrak</td>
                    <td>200</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 12:20:55</td>
                    <td>
                        <button class="btn btn-sm btn-success" type="button" data-toggle="modal" data-target="#confirmModal">Terima Stock</button>
                        <button class="btn btn-sm" type="button" data-toggle="modal" data-target="#editModal">Edit</button>
                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Ibu Dwi</td>
                    <td>PT Jingkrak</td>
                    <td>350</td>
                    <td>25/10/2017 08:20:55</td>
                    <td>-</td>
                    <td>
                        <button class="btn btn-sm btn-success" type="button" data-toggle="modal" data-target="#confirmModal">Terima Stock</button>
                        <button class="btn btn-sm" type="button" data-toggle="modal" data-target="#editModal">Edit</button>
                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal">Delete</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Confirm Modal -->

    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="confirmModalLabel">Terima Stock</h4>
                </div>
                <form>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Konfirmasi terima stok</button>
                    </div>
                </form>

            </div>
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
                        <label for="admin"><strong>Admin</strong></label>
                        <input type="text" class="form-control" name="admin">
                    </div> 
                    <div class="form-group">
                        <label for="outsourcing"><strong>Outsourcing</strong></label>
                        <input type="text" class="form-control" name="outsourcing">
                    </div>                      
                    <div class="form-group">
                        <label for="quantity"><strong>Jumlah Galon</strong></label>
                        <input type="text" class="form-control" name="quantity">
                    </div>                   
                    <div class="form-group">
                        <label for="order_at"><strong>Tgl Order</strong></label>
                        <input type="date" class="form-control" name="order_at">
                    </div>                   
                    <div class="form-group">
                        <label for="accepted_at"><strong>Tgl Penerimaan</strong></label>
                        <input type="date" class="form-control" name="accepted_at">
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

    <!-- Delete Modal -->

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="deleteModalLabel">Delete Data</h4>
                </div>

                <div class="modal-body">                                           
                    <div class="form-group">
                        <label for="description"><strong>Deskripsi Pengubahan Data</strong></label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Close</button>
                </div>
            </form>


        </div>
      </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#gallon_order').dataTable({
                'order':[4, 'desc'],
                scrollX: true,     
                fixedHeader: true,       
                processing: true,
            });
        });
    </script>
@endsection