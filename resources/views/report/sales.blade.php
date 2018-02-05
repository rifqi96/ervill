@extends('layouts.master')

@section('title')
    Laporan Penjualan
@endsection

@section('content')

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <div class="row filterBy">
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Kolom pencarian</h5>
                            </div>
                            <div class="card-block">
                                <form id="filterBy">
                                    <div class="row form-group">
                                        <div class="col-xl-3">Tgl Pengiriman dari:</div>
                                        <div class="col-xl-4">
                                            <input type="date" name="start_date" class="form-control" id="search-date-start" value="{{\Carbon\Carbon::now()->startOfMonth()->format('Y-m-d')}}">
                                        </div>
                                        <div class="col-xl-1">Sampai dengan:</div>
                                        <div class="col-xl-4">
                                            <input type="date" name="end_date" class="form-control" id="search-date-end" value="{{\Carbon\Carbon::now()->endOfMonth()->format('Y-m-d')}}">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-xl-3">Jenis Transaksi:</div>
                                        <div class="col-xl-9 types">
                                            {{--<input type="text" name="nomor_struk" class="form-control" id="search-nostruk" placeholder="OC0000001">--}}
                                            <label for="type-all" class="checkbox-inline">
                                                <input type="checkbox" name="type[]" id="type-all" value="all" checked> Semua Transaksi
                                            </label>
                                            <label for="type-lunas" class="checkbox-inline">
                                                <input type="checkbox" name="type[]" id="type-lunas" value="lunas" disabled> Lunas
                                            </label>
                                            <label for="type-piutang" class="checkbox-inline">
                                                <input type="checkbox" name="type[]" id="type-piutang" value="piutang" disabled> Piutang
                                            </label>
                                            <label for="type-free" class="checkbox-inline">
                                                <input type="checkbox" name="type[]" id="type-free" value="free" disabled> Free/Sample
                                            </label>
                                            <label for="type-retur" class="checkbox-inline">
                                                <input type="checkbox" name="type[]" id="type-retur" value="return" disabled> Retur
                                            </label>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-xl-3"></div>
                                        <div class="col-xl-9">
                                            {{csrf_field()}}
                                            <button type="submit" class="btn btn-primary search-btn">Cari</button>
                                            <button type="reset" class="btn btn-info reset-btn">Reset</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <section class="card" id="print-area">
                <header class="card-header card-header-lg">
                    Laporan Penjualan <div id="period-txt">{{\Carbon\Carbon::parse($report['params']['start_date'])->format('d M Y')}} - {{\Carbon\Carbon::parse($report['params']['end_date'])->format('d M Y')}}</div>
                </header>
                <div class="card-block invoice">
                    <div class="row table-details">
                        <div class="col-lg-12">
                            <table class="table table-bordered" id="report">
                                <thead>
                                <tr>
                                    <th width="10">#</th>
                                    <th>Tanggal</th>
                                    <th>No Faktur</th>
                                    <th>Nama Customer</th>
                                    <th>Jenis</th>
                                    <th>Transaksi</th>
                                    <th>Keterangan</th>
                                    <th>Jumlah Galon</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <td colspan="9">Total:</td>
                                        <td class="grand-total"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-9 col-sm-9 col-xs-6 col-print-9">
                            <button class="btn btn-inline btn-secondary btn-rounded print">Print</button>
                        </div>
                        <div class="col-lg-3 col-sm-3 col-xs-6 col-print-3 clearfix">

                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        var reportTable = function (data) {
            $('#report').DataTable().destroy();
            $('#report').dataTable({
                order:[2, 'desc'],
                fixedHeader: {
                    headerOffset: $('.site-header').outerHeight()
                },
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
                data:data,
                columns: [
                    {data: 'no'},
                    {data: 'delivery_at',
                        render: function (data) {
                            if(data){
                                return moment(data).locale('id').format('DD MMMM YYYY');
                            }
                            return '-';
                        }
                    },
                    {data: 'id'},
                    {data: null,
                        render: function (data) {
                            if(data.customer_id){
                                return '<a href="/setting/customers/id/'+data.customer_id+'" target="_blank">'+data.customer_name+'</a>';
                            }
                            return 'Data tidak ditemukan';
                        }
                    },
                    {data: 'customer_type',
                        render: function (data) {
                            if(data == "end_customer"){
                                return "End Customer";
                            }
                            return 'Agen';
                        }
                    },
                    {data: null,
                        render: function(data){
                            if(data.type == "sales"){
                                if(data.is_only_buy == "true"){

                                }
                            }
                        }
                    }
                ],
                processing: true
            });
        };

        $(document).ready(function () {
            var report = {!! $report['report']['details']->toJson() !!}

            $('.types input[value=all]').change(function () {
                if($(this).is(':checked')){
                    $('.types input[value!=all]').prop('disabled', true);
                    $('.types input[value!=all]').prop('checked', false);
                }
                else{
                    $('.types input[value!=all]').prop('disabled', false);
                }
            });

            reportTable(report);

            $('#filterBy').submit(function (e) {
                e.preventDefault();

                $.post("{{route('report.sales.do.filterby')}}", $(this).serialize())
                    .done(function (result) {
                        console.log('success');
                        console.log(result);
                        reportTable(result);
                    })
                    .fail(function (msg) {
                        console.log('error');
                        console.log(msg);
                    });
                $(this).find('button[type=submit]').prop('disabled', false);
            });

            $('#filterBy .reset-btn').click(function () {
                $('#search-id').val('');
                $('#search-id').trigger('change');
                $('#search-cusname').val('');
                $('#search-cusname').trigger('change');
                $('#search-nostruk').val('');
                $('#search-nostruk').trigger('change');
            });

            $('.numeral').each(function () {
                var price = $(this).text();
                $(this).text(numeral(price).format('$0,0.00'));
            });

            var changeOnScroll = function () {
                if($('body').width() < 580){
                    $('.table').addClass('table-responsive');
                }
                else{
                    $('.table').removeClass('table-responsive');
                }

                if($('body').width() < 780){
                    $('.newhr').show();
                }
                else{
                    $('.newhr').hide();
                }
            };

            changeOnScroll();

            $( window ).on('resize', function () {
                changeOnScroll();
            });

            $('.print').click(function () {
                var contents = $("#print-area").html();
                var frame1 = $('<iframe />');
                frame1[0].name = "frame1";
                frame1.css({ "position": "absolute", "top": "-1000000px" });
                $("body").append(frame1);
                var frameDoc = frame1[0].contentWindow ? frame1[0].contentWindow : frame1[0].contentDocument.document ? frame1[0].contentDocument.document : frame1[0].contentDocument;
                frameDoc.document.open();
                //Create a new HTML document.
                //Append the external CSS file.
                frameDoc.document.write('<link href="{{asset('assets/css/lib/bootstrap/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />');
                frameDoc.document.write('<link href="{{asset('assets/css/print.css')}}" rel="stylesheet" type="text/css" />');
                //Append the DIV contents.
                frameDoc.document.write(contents);
                frameDoc.document.close();
                setTimeout(function () {
                    window.frames["frame1"].focus();
                    window.frames["frame1"].print();
                    frame1.remove();
                }, 500);
            });

        });
    </script>

@endsection