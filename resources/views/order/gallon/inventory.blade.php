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
                    <td>3.000,-</td>
                    <td>10/10/2017 08:20:55</td>
                    <td>15/10/2017 10:20:55</td>
                    <td>
                        <a class="edit ml10" href="javascript:void(0)" title="Edit">
                            <i class="glyphicon glyphicon-edit"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Gallon Isi</td>
                    <td>1000</td>
                    <td>3.000,-</td>
                    <td>10/10/2017 08:20:55</td>
                    <td>15/10/2017 10:20:55</td>
                    <td>
                        <a class="edit ml10" href="javascript:void(0)" title="Edit">
                            <i class="glyphicon glyphicon-edit"></i>
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Gallon Rusak</td>
                    <td>50</td>
                    <td>3.000,-</td>
                    <td>10/10/2017 08:20:55</td>
                    <td>15/10/2017 10:20:55</td>
                    <td>
                        <a class="edit ml10" href="javascript:void(0)" title="Edit">
                            <i class="glyphicon glyphicon-edit"></i>
                        </a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#gallon_inventory').dataTable({
                scrollX: true,  
                fixedHeader: true,       
                processing: true,
                'order':[4, 'asc']
            });
        });
    </script>
@endsection