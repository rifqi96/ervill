@extends('layouts.master')

@section('title')
Overview
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="row">
                <div class="col-sm-6">
                    <article class="statistic-box red">
                        <div>
                            <div class="number">2</div>
                            <div class="caption"><div>Masalah</div></div>
                        </div>
                    </article>
                </div><!--.col-->
                <div class="col-sm-6">
                    <article class="statistic-box yellow">
                        <div>
                            <div class="number">4</div>
                            <div class="caption"><div>Order Berlangsung</div></div>
                        </div>
                    </article>
                </div><!--.col-->
            </div><!--.row-->
        </div><!--.col-->
    </div>

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading">
                <h3 class="panel-title">Order Terbaru</h3>
            </header>
            <table class="table table-hover" id="recent_order">
                <thead>
                <th>Status</th>
                <th>Nama Customer</th>
                <th>Jumlah</th>
                <th align="center">Waktu</th>
                <th>Action</th>
                </thead>
                <tbody>
                <tr>
                    <td><span class="label label-warning">Proses</span></td>
                    <td>Abi</td>
                    <td>5</td>
                    <td>1 Jam Lalu</td>
                    <td><button class="btn btn-sm">Live Tracking</button></td>
                </tr>
                <tr>
                    <td><span class="label label-success">Selesai</span></td>
                    <td>Safira</td>
                    <td>2</td>
                    <td>2 Jam Lalu</td>
                    <td><button class="btn btn-sm">Tracking History</button></td>
                </tr>
                <tr>
                    <td><span class="label label-danger">Bermasalah</span></td>
                    <td>Hasbur</td>
                    <td>3</td>
                    <td>5 Jam Lalu</td>
                    <td><button class="btn btn-sm">Tracking History</button></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading">
                <h3 class="panel-title">Masalah Terbaru</h3>
            </header>
            <table class="table table-hover" id="recent_issue">
                <thead>
                <th>Status</th>
                <th>Nama Customer</th>
                <th>Jumlah</th>
                <th>Nama Driver</th>
                <th align="center">Waktu</th>
                <th>Aksi</th>
                </thead>
                <tbody>
                <tr>
                    <td><span class="label label-danger">Ditukar</span></td>
                    <td>Abi</td>
                    <td>1</td>
                    <td>Saipul</td>
                    <td>1 Jam Lalu</td>
                    <td><button class="btn btn-sm">Selengkapnya...</button></td>
                </tr>
                <tr>
                    <td><span class="label label-danger">Retur</span></td>
                    <td>Sabrina</td>
                    <td>2</td>
                    <td>Saipul</td>
                    <td>1 Jam Lalu</td>
                    <td><button class="btn btn-sm">Selengkapnya...</button></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
           $('#recent_order').dataTable({
                scrollX: true,    
                fixedHeader: true,       
                processing: true,
               'order':[3, 'asc']
           });

            $('#recent_issue').dataTable({
                scrollX: true,  
                fixedHeader: true,       
                processing: true,
                'order':[3, 'asc']
            });
        });
    </script>
@endsection