@extends('layouts.master')

@section('title')
Detail Faktur
@endsection

@section('content')

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <div class="row">
                    <div class="col-xl-10 col-sm-10 col-xs-9">
                        <a href="{{route('invoice.sales.index')}}"><button class="btn btn-primary">Lihat Daftar Faktur Penjualan</button></a>
                    </div>
                    <div class="col-xl-2 col-sm-2 col-xs-3">
                        <button class="btn btn-inline btn-secondary btn-rounded print">Print</button>
                    </div>
                </div>
            </header>

            <section class="card" id="print-area">
                <header class="card-header card-header-lg">
                    Faktur Penjualan - {{$invoice->payment_status_txt}}
                </header>
                <div class="card-block invoice">
                    <div class="row">
                        <div class="col-lg-6 col-md-8 col-print-6 company-info">
                            <h5>ERVILL</h5>

                            <div class="invoice-block">
                                <div>Jl. Imam Bonjol No. 27 E</div>
                                <div>Karawaci</div>
                                <div>Tangerang</div>
                            </div>

                            <div class="invoice-block">
                                <div>Telephone: (021) 5585050</div>
                            </div>
                        </div>
                        <div class="newhr">
                            <hr>
                        </div>
                        <div class="col-lg-6 col-md-4 col-print-6 clearfix invoice-info">
                            <div class="text-lg-right">
                                <h5>Nomor Faktur {{$invoice->id}}</h5>
                                @if($invoice->has_order)
                                <div>
                                    Tgl Pengiriman:
                                    <b class="delivery-at">{{\Carbon\Carbon::parse($invoice->delivery_at)->format('d-m-Y')}}</b>
                                </div>
                                <div>
                                    Tgl Pembayaran:
                                    <b class="payment-at">{{ $invoice->payment_status && $invoice->is_free == "false" && $invoice->payment_date ? \Carbon\Carbon::parse($invoice->payment_date)->format('d-m-Y H:i:s') : '-' }}</b>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($invoice->has_order)
                    <hr>
                    <div class="row">
                        <div class="col-lg-12 col-print-12">
                            <div class="invoice-block">
                                <h5>Pemesanan untuk:</h5>
                                <div>Ibu/Bapak {{$invoice->customer_name}}</div>
                                <div>Alamat: {{$invoice->customer_address}}</div>
                                <div>No. HP: {{$invoice->customer_phone}}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row table-details">
                        <div class="col-lg-12">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th width="10">#</th>
                                    <th>Keterangan</th>
                                    <th>Jumlah (Galon)</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $i = 1;
                                    @endphp
                                    @foreach($invoice->orderCustomerInvoices as $row)
                                        <tr>
                                            <td>{{$i++}}</td>
                                            <td>
                                                {{$row->price->name}}
                                            </td>
                                            <td>
                                                {{$row->quantity}}
                                            </td>
                                            <td class="numeral">
                                                {{$invoice->is_free == "false" ? $row->price->price : 0}}
                                            </td>
                                            <td class="numeral total">
                                                {{$invoice->is_free == "false" ? $row->subtotal : 0}}
                                            </td>
                                        </tr>                                    
                                    @endforeach
                                     @foreach($invoice->orderCustomerBuyInvoices as $row)
                                        <tr>
                                            <td>{{$i++}}</td>
                                            <td>
                                                {{$row->price->name}}
                                            </td>
                                            <td>
                                                {{$row->quantity}}
                                            </td>
                                            <td class="numeral">
                                                {{$invoice->is_free == "false" ? $row->price->price : 0}}
                                            </td>
                                            <td class="numeral total">
                                                {{$invoice->is_free == "false" ? $row->subtotal : 0}}
                                            </td>
                                        </tr>                                    
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-lg-9 col-sm-9 col-xs-6 col-print-9">
                            <strong>S&K</strong>
                            <p>Terima kasih telah membeli air mineral berkualitas di ERVILL.</p>
                        </div>
                        <div class="col-lg-3 col-sm-3 col-xs-6 col-print-3 clearfix">
                            <div class="total-amount">
                                <div>Total: <b class="numeral grand-total"></b></div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var grand_total = 0;
            $('.total').each(function () {
               grand_total += parseInt($(this).text());
            });
            $('.grand-total').text(grand_total);
            $('.numeral').each(function () {
                var price = $(this).text();
                $(this).text(numeral(price).format('$0,0'));
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