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
                <th align="center">Tgl Pengiriman</th>
                <th align="center">Tgl Penerimaan</th>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>Ibu Dwi</td>
                    <td>PT Jingkrak</td>
                    <td>200</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017 10:20:55</td>
                    <td>20/10/2017 12:20:55</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Ibu Dwi</td>
                    <td>PT Jingkrak</td>
                    <td>350</td>
                    <td>25/10/2017 08:20:55</td>
                    <td>26/10/2017 10:20:55</td>
                    <td>26/10/2017 12:20:55</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('#gallon_order').dataTable({
                'order':[4, 'asc']
            });
        });
    </script>
@endsection