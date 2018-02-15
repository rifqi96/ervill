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

            <section class="" id="print-area">
                <h2 class="report-title">
                    Laporan Penjualan <div id="period-txt">{{\Carbon\Carbon::parse($report['params']['start_date'])->format('d M Y')}} - {{\Carbon\Carbon::parse($report['params']['end_date'])->format('d M Y')}}</div>
                </h2>
                <div class="invoice">
                    <div class="row table-details">
                        <div class="col-lg-12">
                            <table class="table table-hover" id="report">
                                <thead>
                                <tr>
                                    <th width="10">#</th>
                                    <th>Tanggal</th>
                                    <th>No Faktur</th>
                                    <th>Nama Customer</th>
                                    <th>Jenis Customer</th>
                                    <th>Transaksi</th>
                                    <th>Keterangan</th>
                                    <th>Jumlah Galon</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <td><b>Total</b></td>
                                        <td colspan="8"></td>
                                        <td><b class="numeral grand-total"></b></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        var reportTable = function (data) {
            var start_date = moment($('#search-date-start').val()).locale('id').format('DD/MM/YYYY');
            var end_date = moment($('#search-date-end').val()).locale('id').format('DD/MM/YYYY');
            $('#period-txt').text(start_date+' - '+end_date);
            var period_txt = "<h2>Periode " + $('#period-txt').text() + "</h2>";
            $('#report').DataTable().destroy();
            $('#report').dataTable({
                order:[0, 'asc'],
                fixedHeader: {
                    headerOffset: $('.site-header').outerHeight()
                },
                select: {
                    style: 'multi'
                },
                dom: 'Bfrtip',
                buttons: [
                    { extend: 'excel', text:'Simpan ke Excel', messageTop: period_txt, footer:true, className:'btn btn-success btn-sm', exportOptions: {
                        columns: ':visible'
                    }},
                    { extend: 'print', text:'Cetak', messageTop: period_txt, className:'btn btn-warning btn-sm', footer:true, exportOptions: {
                        columns: ':visible'
                    }},
                    { extend: 'colvis', text:'Pilih Kolom', className:'btn btn-default btn-sm'}

                ],
                bPaginate: false,
                data:data,
                columns: [
                    {data: 'no'},
                    {data: 'delivery_at',
                        render: function (data) {
                            if(data){
                                return moment(data).locale('id').format('DD/MM/YYYY');
                            }
                            return '-';
                        }
                    },
                    {data: null,
                        render: function (data) {
                            if(data.type == "sales"){
                                return '<a href="/invoice/sales/id/'+data.oc_header_invoice_id+'" onclick="window.open(this.href, \'Struk\', \'left=300,top=50,width=800,height=500,toolbar=1,resizable=1, scrollable=1\'); return false;">'+data.oc_header_invoice_id+'</a>';
                            }
                            else if(data.type == "return"){
                                return '<a href="/invoice/return/id/'+data.re_header_invoice_id+'" onclick="window.open(this.href, \'Struk\', \'left=300,top=50,width=800,height=500,toolbar=1,resizable=1, scrollable=1\'); return false;">'+data.re_header_invoice_id+'</a>';
                            }
                            return 'Data tidak ditemukan';
                        }
                    },
                    {data: null,
                        render: function (data) {
                            if(data.type == "sales"){
                                return '<a href="/setting/customers/id/'+data.customer.id+'" target="_blank">'+data.customer.name+'</a>';
                            }
                            else if(data.type == "return"){
                                return '<a href="/setting/customers/id/'+data.order_customer_return.customer.id+'" target="_blank">'+data.order_customer_return.customer.name+'</a>';
                            }
                            return 'Data tidak ditemukan';
                        }
                    },
                    {data: null,
                        render: function (data) {
                            if(data.type == "sales"){
                                if(data.customer.type == "end_customer"){
                                    return "End Customer";
                                }
                                return "Agen"
                            }
                            else if(data.type == "return"){
                                if(data.order_customer_return.customer.type == "end_customer"){
                                    return "End Customer";
                                }
                                return "Agen"
                            }

                            return 'Data tidak ditemukan';
                        }
                    },
                    {data: null,
                        render: function(data){
                            if(data.type == "sales"){
                                if(data.price_master){
                                    return data.name;
                                }
                            }
                            else if(data.type == "return"){
                                return data.price.name + " (" +data.payment_status+")";
                            }

                            return 'Data tidak ditemukan';
                        }
                    },
                    {data: null,
                        render: function (data) {
                            if(data.type == "return"){
                                return data.order_customer_return.description;
                            }
                            return "";
                        }
                    },
                    {data: 'quantity'},
                    {data: null,
                        render: function (data) {
                            if(data.payment_status == "Non Refund"){
                                return '<div class="numeral">0</div>';
                            }
                            else if(data.payment_status == "Refund"){
                                return '<div class="numeral">'+data.price_number * (-1)+'</div>';
                            }
                            else if(data.type == "sales" && data.is_free == "true"){
                                return '<div class="numeral">0</div>';
                            }

                            return '<div class="numeral">'+data.price+'</div>';
                        }
                    },
                    {data: null,
                        render: function (data) {
                            if(data.payment_status == "Non Refund"){
                                return '<div class="numeral total">0</div>';
                            }
                            else if(data.payment_status == "Refund"){
                                return '<div class="numeral total">'+data.subtotal * (-1)+'</div>';
                            }
                            else if(data.type == "sales" && data.is_free == "true"){
                                return '<div class="numeral">0</div>';
                            }

                            return '<div class="numeral total">'+data.subtotal+'</div>';
                        }
                    }
                ],
                processing: true
            });

            var grand_total = 0;
            $('.total').each(function () {
                grand_total += parseInt($(this).text());
            });
            $('.grand-total').text(grand_total);
            $('.numeral').each(function () {
                var price = $(this).text();
                $(this).text(numeral(price).format('$0,0'));
            });
        };

        $(document).ready(function () {
            var report = {!! $report['details']->toJson() !!}
            reportTable(report);

            $('.types input[value=all]').change(function () {
                if($(this).is(':checked')){
                    $('.types input[value!=all]').prop('disabled', true);
                    $('.types input[value!=all]').prop('checked', false);
                }
                else{
                    $('.types input[value!=all]').prop('disabled', false);
                }
            });

            $('#filterBy').submit(function (e) {
                e.preventDefault();

                $.post("{{route('report.sales.do.filterby')}}", $(this).serialize())
                    .done(function (result) {
                        console.log('success');
                        console.log(result);
                        reportTable(result);
                    })
                    .fail(function (res) {
                        console.log('error');
                        console.log(res.responseJSON);

                        var response = res.responseJSON;
                        var msg = "";

                        if(response.type){
                            for(var i=0; i<response.type.length; i++){
                                msg += response.type[i] + " ; ";
                            }
                        }
                        if(response.start_date){
                            for(var i=0; i<response.start_date.length; i++){
                                msg += response.start_date[i] + " ; ";
                            }
                        }
                        if(response.end_date){
                            for(var i=0; i<response.end_date.length; i++){
                                msg += response.end_date[i] + " ; ";
                            }
                        }

                        alert(msg);
                    });
                $(this).find('button[type=submit]').prop('disabled', false);
            });

            $('#filterBy .reset-btn').click(function () {
                $('#search-nostruk').val('');
                $('#search-nostruk').trigger('change');
            });

            $('.numeral').each(function () {
                var price = $(this).text();
                $(this).text(numeral(price).format('$0,0'));
            });

        });
    </script>

@endsection