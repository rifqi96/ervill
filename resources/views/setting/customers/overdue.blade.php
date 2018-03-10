@extends('layouts.master')

@section('title')
    Customer Overdue Per Tgl {{\Carbon\Carbon::now()->format('d/m/Y')}}
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            {{--<header class="box-typical-header panel-heading" style="margin-bottom: 30px;">--}}
            {{--<h3 class="panel-title"></h3>--}}
            {{--<a href="{{route('setting.customers.make')}}"><button class="btn btn-primary">Tambah Customer Baru</button></a>--}}
            {{--</header>--}}
            <table class="table table-hover" id="customers">
                <thead>
                <th>No</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>No. Telepon</th>
                <th>Jenis</th>
                <th>Hari Overdue</th>
                <th>Pengiriman Terakhir</th>
                <th>Tgl Overdue</th>
                <th>Diperingatkan setiap</th>
                <th>Action</th>
                </thead>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var customers = {!! $customers->toJson() !!};

            $('#customers').dataTable({
                fixedHeader: {
                    headerOffset: $('.site-header').outerHeight()
                },
                processing: true,
                order:[0, 'desc'],
                select: {
                    style: 'multi'
                },
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'excel', text:'Simpan ke Excel', className:'btn btn-success btn-sm', exportOptions: {
                        columns: ':visible'
                    }},
                    { extend: 'print', text:'Cetak', className:'btn btn-warning btn-sm', exportOptions: {
                        columns: ':visible'
                    }},
                    { extend: 'colvis', text:'Pilih Kolom', className:'btn btn-default btn-sm'}

                ],
                data:customers,
                columns: [
                    {data: 'id'},
                    {data: 'name'},
                    {data: 'address'},
                    {data: 'phone'},
                    {data: 'type',
                        render: function(data) {
                            if(data == "end_customer")
                                return "End Customer";
                            else if(data == "agent")
                                return "Agen";

                            return "-";
                        }
                    },
                    {data: 'overdue',
                        render: function (data) {
                            if(data){
                                if(data == 0){
                                    return '<span style="color:blue;">Hari ini</span>';
                                }
                                return '<span style="color:red;">Lewat ' + Math.abs(data) + ' hari</span>';
                            }
                            return '-';
                        }
                    },
                    {data: 'last_transaction',
                        render: function (data) {
                            if(data){
                                return moment(data).locale('id').format('DD/MM/YYYY');
                            }
                            return '-';
                        }
                    },
                    {data: 'overdue_date',
                        render: function (data) {
                            if(data){
                                return '<span style="color:red;">' + moment(data).locale('id').format('DD/MM/YYYY') + '</span>';
                            }
                            return '-';
                        }
                    },
                    {data: 'notif_day',
                        render: function (data) {
                            if(data){
                                return data + ' hari dari pengiriman terakhir';
                            }

                            return '10 hari dari pengiriman terakhir';
                        }
                    },
                    {
                        data: null,
                        render: function ( data, type, row, meta ) {
                            return '<a href="id/'+row.id+'" target="_blank"><button class="btn btn-sm detail-btn" type="button">Lihat</button></a>';

                        }
                    }
                ]
            });
        });
    </script>
@endsection