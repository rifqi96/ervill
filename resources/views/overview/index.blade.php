@extends('layouts.master')

@section('title')
Overview
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="row">
                <div class="col-sm-6">
                    @if($recent_issues->count() > 0)
                        <a href="{{route('order.customers.index')}}">
                            <article class="statistic-box red">
                                <div>
                                    <div class="number">{{$recent_issues->count()}}</div>
                                    <div class="caption"><div>Masalah</div></div>
                                </div>
                            </article>
                        </a>
                    @else
                        <article class="statistic-box green">
                            <div>
                                <div class="number">0</div>
                                <div class="caption"><div>Masalah</div></div>
                            </div>
                        </article>
                    @endif
                </div><!--.col-->
                <div class="col-sm-6">
                    @if($process_orders->count() > 0)
                        <a href="{{route('order.customers.index')}}">
                            <article class="statistic-box yellow">
                                <div>
                                    <div class="number">{{$process_orders->count()}}</div>
                                    <div class="caption"><div>Order Berlangsung</div></div>
                                </div>
                            </article>
                        </a>
                    @else
                        <article class="statistic-box green">
                            <div>
                                <div class="number">{{$process_orders->count()}}</div>
                                <div class="caption"><div>Order Berlangsung</div></div>
                            </div>
                        </article>
                    @endif
                </div><!--.col-->
            </div><!--.row-->
            <div class="row">
                <div class="col-sm-12">
                    @if($overdue_customers->count() > 0)
                        <a href="{{route('setting.customers.overdue')}}">
                            <article class="statistic-box yellow">
                                <div>
                                    <div class="number">{{$overdue_customers->count()}}</div>
                                    <div class="caption"><div>Customer Overdue</div></div>
                                </div>
                            </article>
                        </a>
                    @else
                        <article class="statistic-box green">
                            <div>
                                <div class="number">{{$overdue_customers->count()}}</div>
                                <div class="caption"><div>Customer Overdue</div></div>
                            </div>
                        </article>
                    @endif
                </div>
            </div>
        </div><!--.col-->
    </div>

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading">
                <h3 class="panel-title">Order Terbaru</h3>
            </header>
            <table class="table table-hover" id="recent-orders">
                <thead>
                <th>Status</th>
                <th>Nama Customer</th>
                <th>No. Telepon</th>
                <th>Alamat Customer</th>
                <th>Nama Driver</th>
                <th>Galon Isi Keluar</th>
                <th>Galon Masuk Kosong Ervill</th>
                <th>Galon Masuk Kosong Non Ervill</th>
                <th align="center">Waktu</th>
                <th>Admin</th>
                <!-- <th>Action</th> -->
                </thead>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading">
                <h3 class="panel-title">Masalah Terbaru</h3>
            </header>
            <table class="table table-hover" id="recent-issues">
                <thead>
                <th>Status</th>
                <th>Nama Customer</th>
                <th>Jumlah</th>
                <th>Nama Driver</th>
                <th align="center">Waktu</th>
                <th>Aksi</th>
                </thead>
            </table>
        </div>
    </div>

    <!-- Driver Modal -->
    <div class="modal fade" id="issueModal" tabindex="-1" role="dialog" aria-labelledby="issueModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="issueModalLabel">Detail Masalah</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="issue-status"><strong>Status Masalah</strong></label>
                        <p class="form-control-static" id="issue-status"></p>
                    </div>
                    <div class="form-group">
                        <label for="issue-qty"><strong>Jumlah Galon</strong></label>
                        <p class="form-control-static" id="issue-qty"></p>
                    </div>
                    <div class="form-group">
                        <label for="issue-description"><strong>Deskripsi Masalah</strong></label>
                        <p class="form-control-static" id="issue-description"></p>
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
            var recent_orders = JSON.parse('{!! $recent_orders !!}');
            var recent_issues = JSON.parse('{!! $recent_issues !!}');

            $('#recent-orders').dataTable({
                scrollX:true,
                scrollY: 250,
                scrollCollapse:true,
                processing: true,
                order:[8, 'desc'],
                data:recent_orders,
                columns:[
                    {data: null,
                        render: function(data, type, row, meta){
                            if(data.status == "Selesai"){
                                return '<span class="label label-success">Selesai</span>';
                            }
                            else if(data.status == "Proses"){
                                return '<span class="label label-warning">Proses</span>';
                            }
                            else if(data.status == "Bermasalah"){
                                return '<span class="label label-danger">Bermasalah</span>';
                            }
                            else{
                                return '<span class="label label-info">Draft</span>';
                            }
                        }},
                    {data: null,
                        render: function(data){
                            if(data.customer){
                                return data.customer.name;
                            }
                            return '<i>Data customer tidak ditemukan</i>';
                        }},
                    {data: null,
                        render: function(data){
                            if(data.customer){
                                return data.customer.phone;
                            }
                            return '<i>Data customer tidak ditemukan</i>';
                        }},
                    {data: null,
                        render: function(data){
                            if(data.customer){
                                return data.customer.address;
                            }
                            return '<i>Data customer tidak ditemukan</i>';
                        }},
                    {data: null,
                        render: function(data){
                            if(data.shipment){
                                return data.shipment.user.full_name;
                            }
                            else{
                                return '-';
                            }
                        }},
                    {data: null,
                        render: function (data) {
                            return data.additional_quantity+data.order.quantity;
                        }
                    },
                    {data: null,
                        render: function (data) {
                            if(data.purchase_type == 'non_ervill'){
                                if(data.is_new == 'true'){
                                    return 0;
                                }
                                else if(data.is_new == 'false'){
                                    return data.order.quantity;
                                }
                            }

                            return data.empty_gallon_quantity;
                        }},
                    {data: null,
                        render: function (data) {
                            if(data.purchase_type == 'non_ervill'){
                                if(data.is_new == 'true'){
                                    return data.order.quantity;
                                }
                                else if(data.is_new == 'false'){
                                    return data.additional_quantity;
                                }
                            }

                            return 0;
                        }},
                    {data: null,
                    render: function (data) {
                        var date = moment(data.order.created_at, 'YYYY-MM-DD HH:mm:ss');
                        date.locale('id');
                        return date.calendar();
                    }},
                    {data:null,
                    render: function (data) {
                        if(data.order.user){
                            return '<a href="/setting/user_management/id/'+data.order.user.id+'" target="_blank" title="Klik untuk lihat">'+data.order.user.full_name+'</a>';
                        }
                        return 'Data admin tidak ditemukan';
                    }},
                    // {data: null,
                    //     render: function(data, type, row, meta){
                    //         var result = "";
                    //         if(data.status != "Draft"){
                    //             if(data.shipment){
                    //                 var shipment_url = "{{route("shipment.track", ":id")}}";
                    //                 shipment_url = shipment_url.replace(':id', data.shipment.id);
                    //                 if(data.status == "Proses"){
                    //                     result += '<a class="btn btn-sm" href="'+shipment_url+'" target="_blank">Live Tracking</a>';
                    //                 }
                    //                 else if(data.status == "Bermasalah" || data.status == "Selesai"){
                    //                     result += '<a class="btn btn-sm" href="'+shipment_url+'" target="_blank">Tracking History</a>';
                    //                 }
                    //             }
                    //         }

                    //         return result;
                    //     }}
                ]
            });

            $('#recent-issues').dataTable({
                scrollX:true,
                scrollY: 250,
                scrollCollapse:true,
                processing: true,
                order:[4, 'desc'],
                data:recent_issues,
                columns:[
                    {data: null,
                        render: function(data, type, row, meta){
                            return '<span class="label label-danger">'+data.type+'</span>';
                        }},
                    {data: null,
                        render: function(data){
                            if(data.order.order_customer.customer){
                                return data.order.order_customer.customer.name;
                            }
                            return '<i>Data customer tidak ditemukan</i>';
                        }},
                    {data: 'quantity'},
                    {data: null,
                        render: function(data){
                            if(data.order.order_customer.shipment){
                                return data.order.order_customer.shipment.user.full_name;
                            }
                            else{
                                return '-';
                            }
                        }},
                    {data: null,
                        render: function (data) {
                            var date = moment(data.created_at, 'YYYY-MM-DD HH:mm:ss');
                            date.locale('id');
                            return date.calendar();
                        }},
                    {data:null,
                    render: function (data) {
                        return '<a class="btn btn-sm issue-modal-btn" href="#" data-toggle="modal" data-target="#issueModal" data-index="'+data.id+'">Detail Masalah</a>';
                    }}
                ]
            });

            // On issue modal button being clicked
            $('#recent-issues').on('click', '.issue-modal-btn', function () {
                for(var i in recent_issues){
                    if(recent_issues[i].id == $(this).data('index')){
                        $('#issue-qty').text(recent_issues[i].quantity);
                        $('#issue-status').text(recent_issues[i].type);
                        $('#issue-description').text(recent_issues[i].description);
                    }
                }
            });
        });
    </script>
@endsection