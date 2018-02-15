@extends('layouts.master')

@section('title')
Overview
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12">
            <div class="row">
                <div class="col-sm-6">
                    @if($recent_orders->count() > 0)
                        <article class="statistic-box yellow">
                            <div>
                                <div class="number">{{$recent_orders->count()}}</div>
                                <div class="caption"><div>Order Hari Ini</div></div>
                            </div>
                        </article>
                    @else
                        <article class="statistic-box green">
                            <div>
                                <div class="number">{{$recent_orders->count()}}</div>
                                <div class="caption"><div>Order Hari Ini</div></div>
                            </div>
                        </article>
                    @endif
                </div><!--.col-->
                <div class="col-sm-6">
                    @if($process_orders->count() > 0)
                        <article class="statistic-box yellow">
                            <div>
                                <div class="number">{{$process_orders->count()}}</div>
                                <div class="caption"><div>Order Berlangsung</div></div>
                            </div>
                        </article>
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
                <div class="col-sm-6">
                    @if($piutang_invoices->count() > 0)
                        <a href="{{route('invoice.sales.index')}}#piutang_invoices">
                            <article class="statistic-box yellow">
                                <div>
                                    <div class="number">{{$piutang_invoices->count()}} / <span class="numeral">{{$total_piutang}}</span></div>
                                    <div class="caption"><div>Faktur Piutang / Jumlah Piutang</div></div>
                                </div>
                            </article>
                        </a>
                    @else
                        <article class="statistic-box green">
                            <div>
                                <div class="number">0</div>
                                <div class="caption"><div>Faktur Piutang</div></div>
                            </div>
                        </article>
                    @endif
                </div><!--.col-->
                <div class="col-sm-6">
                    @if($piutang_invoices->count() > 0)
                        <a href="{{route('setting.customers.overdue')}}">
                            <article class="statistic-box yellow">
                                <div>
                                    <div class="number">{{$piutang_invoices->count()}}</div>
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
                <th>No Faktur</th>
                <th>Nama Customer</th>
                <th>No. Telepon</th>
                <th>Alamat Customer</th>
                <th>Nama Driver</th>
                {{--<th>Galon Isi Keluar</th>--}}
                {{--<th>Galon Masuk Kosong Ervill</th>--}}
                {{--<th>Galon Masuk Kosong Non Ervill</th>--}}
                <th align="center">Waktu</th>
                <th>Admin</th>
                <!-- <th>Action</th> -->
                </thead>
            </table>
        </div>
    </div>

    {{--<div class="row">--}}
        {{--<div class="col-xl-12 dashboard-column">--}}
            {{--<header class="box-typical-header panel-heading">--}}
                {{--<h3 class="panel-title">Masalah Terbaru</h3>--}}
            {{--</header>--}}
            {{--<table class="table table-hover" id="recent-issues">--}}
                {{--<thead>--}}
                {{--<th>Status</th>--}}
                {{--<th>Nama Customer</th>--}}
                {{--<th>Jumlah</th>--}}
                {{--<th>Nama Driver</th>--}}
                {{--<th align="center">Waktu</th>--}}
                {{--<th>Aksi</th>--}}
                {{--</thead>--}}
            {{--</table>--}}
        {{--</div>--}}
    {{--</div>--}}

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
            var recent_orders = {!! $recent_orders->toJson() !!};
            {{--var recent_issues = {!! $recent_issues->toJson() !!};--}}

            $('#recent-orders').dataTable({
                scrollX:true,
                scrollY: 250,
                scrollCollapse:true,
                processing: true,
                order:[1, 'desc'],
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
                            else if(data.status == "Batal"){
                                return '<span class="label label-danger">Batal</span>';
                            }

                            return '<span class="label label-info">Draft</span>';
                        }},
                    {data: null,
                        render:function (data) {
                            if(data.invoice_no){
                                if(data.type == "sales"){
                                    return '<a href="invoice/sales/id/'+data.invoice_no+'" onclick="window.open(this.href, \'Struk\', \'left=300,top=50,width=800,height=500,toolbar=1,resizable=1, scrollable=1\'); return false;">'+data.invoice_no+'</a>';
                                }
                                return '<a href="invoice/return/id/'+data.invoice_no+'" onclick="window.open(this.href, \'Struk\', \'left=300,top=50,width=800,height=500,toolbar=1,resizable=1, scrollable=1\'); return false;">'+data.invoice_no+'</a>';
                            }
                            return '-';
                        }
                    },
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
                        var date = moment(data.updated_at, 'YYYY-MM-DD HH:mm:ss');
                        date.locale('id');
                        return date.calendar();
                    }},
                    {data:null,
                    render: function (data) {
                        if(data.user){
                            return '<a href="/setting/user_management/id/'+data.user.id+'" target="_blank" title="Klik untuk lihat">'+data.user.full_name+'</a>';
                        }
                        return 'Data admin tidak ditemukan';
                    }},
                ]
            });

            $('.numeral').each(function () {
                var price = $(this).text();
                $(this).text(numeral(price).format('$0,0'));
            });

//            $('#recent-issues').dataTable({
//                scrollX:true,
//                scrollY: 250,
//                scrollCollapse:true,
//                processing: true,
//                order:[4, 'desc'],
//                data:recent_issues,
//                columns:[
//                    {data: null,
//                        render: function(data, type, row, meta){
//                            return '<span class="label label-danger">'+data.type+'</span>';
//                        }},
//                    {data: null,
//                        render: function(data){
//                            if(data.order.order_customer.customer){
//                                return data.order.order_customer.customer.name;
//                            }
//                            return '<i>Data customer tidak ditemukan</i>';
//                        }},
//                    {data: 'quantity'},
//                    {data: null,
//                        render: function(data){
//                            if(data.order.order_customer.shipment){
//                                return data.order.order_customer.shipment.user.full_name;
//                            }
//                            else{
//                                return '-';
//                            }
//                        }},
//                    {data: null,
//                        render: function (data) {
//                            var date = moment(data.created_at, 'YYYY-MM-DD HH:mm:ss');
//                            date.locale('id');
//                            return date.calendar();
//                        }},
//                    {data:null,
//                    render: function (data) {
//                        return '<a class="btn btn-sm issue-modal-btn" href="#" data-toggle="modal" data-target="#issueModal" data-index="'+data.id+'">Detail Masalah</a>';
//                    }}
//                ]
//            });
//
//            // On issue modal button being clicked
//            $('#recent-issues').on('click', '.issue-modal-btn', function () {
//                for(var i in recent_issues){
//                    if(recent_issues[i].id == $(this).data('index')){
//                        $('#issue-qty').text(recent_issues[i].quantity);
//                        $('#issue-status').text(recent_issues[i].type);
//                        $('#issue-description').text(recent_issues[i].description);
//                    }
//                }
//            });
        });
    </script>
@endsection