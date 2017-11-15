@extends('layouts.master')

@section('title')
    Delete History
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <table class="table table-hover" id="water_order">
                <thead>
                <th>ID</th>
                <th>Nama Modul</th>
                <th>Tgl Delete</th>
                <th>Action</th>
                </thead>
                <tbody>
                <tr>
                    <td>2</td>
                    <td>User Management</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#detailModal">Detail</button>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Order Galon</td>
                    <td>20/10/2017 08:20:57</td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#detailModal">Detail</button>
                    </td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>Order Air</td>
                    <td>20/10/2017 08:20:56</td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#detailModal">Detail</button>
                    </td>
                </tr>
                </tbody>
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
                <form>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Kembalikan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <script>
        $(document).ready(function () {
            $('#water_order').dataTable({
                scrollX: true,
                fixedHeader: true,
                processing: true,
                'order':[2, 'desc']
            });

            $('#issues').dataTable({
                fixedHeader: true,
                processing: true
            });
        });
    </script>

@endsection