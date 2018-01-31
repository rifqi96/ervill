@extends('layouts.master')

@section('title')
Detail Pesanan
@endsection

@section('content')

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <div class="row">
                    <div class="col-xl-10 col-sm-10 col-xs-9">
                        <a href="{{route('order.customer.index')}}"><button class="btn btn-primary">Lihat Pesanan Customer</button></a>
                    </div>
                    <div class="col-xl-2 col-sm-2 col-xs-3">
                        <button class="btn btn-inline btn-secondary btn-rounded print">Print</button>
                    </div>
                </div>
            </header>

            <section class="card" id="print-area">
                <header class="card-header card-header-lg">
                    Struk Pemesanan
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
                                <h5>Nomor Transaksi/Struk {{$oc->nomor_struk?$oc->nomor_struk:$oc->id}}</h5>
                                <div>Tanggal Pengiriman: <b class="delivery-at">{{\Carbon\Carbon::parse($oc->delivery_at)->format('d-m-Y')}}</b></div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-lg-12 col-print-12">
                            <div class="invoice-block">
                                <h5>Pemesanan untuk:</h5>
                                <div>Ibu/Bapak {{$oc->customer->name}}</div>
                                <div>Alamat: {{$oc->customer->address}}</div>
                                <div>No. HP: {{$oc->customer->phone=='0000'?"-":$oc->customer->phone}}</div>
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
                                    <th>Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($details as $key => $val)
                                    <tr>
                                        <td>{{$i=$key+1}}</td>
                                        <td>
                                            @if($details[$key]->purchase_type)
                                                @if($details[$key]->purchase_type == "purchase")
                                                    Pembelian galon
                                                @elseif($details[$key]->purchase_type == "rent")
                                                    Peminjaman galon
                                                @else
                                                    Tukar galon merk lain
                                                @endif
                                            @else
                                                Isi ulang air
                                            @endif
                                        </td>
                                        <td>
                                            @if($details[$key]->purchase_type)
                                                @if($details[$key]->purchase_type == "purchase")
                                                    @if($details[$key]->is_new == "false")
                                                        {{$details[$key]->additional_quantity}}
                                                    @else
                                                        {{$details[$key]->order->quantity}}
                                                    @endif
                                                @elseif($details[$key]->purchase_type == "rent")
                                                    @if($details[$key]->is_new == "false")
                                                        {{$details[$key]->additional_quantity}}
                                                    @else
                                                        {{$details[$key]->order->quantity}}
                                                    @endif
                                                @else
                                                    @if($details[$key]->is_new == "false")
                                                        {{$details[$key]->additional_quantity}}
                                                    @else
                                                        {{$details[$key]->order->quantity}}
                                                    @endif
                                                @endif
                                            @else
                                                {{$details[$key]->order->quantity}}
                                            @endif
                                        </td>
                                        <td class="numeral">
                                            @if($details[$key]->purchase_type)
                                                @if($details[$key]->purchase_type == "purchase")
                                                    @if($details[$key]->customer->type == "end_customer")
                                                        42000
                                                    @else
                                                        40000
                                                    @endif
                                                @elseif($details[$key]->purchase_type == "rent")
                                                    @if($details[$key]->customer->type == "end_customer")
                                                        12000
                                                    @else
                                                        10000
                                                    @endif
                                                @else
                                                    @if($details[$key]->customer->type == "end_customer")
                                                        12000
                                                    @else
                                                        10000
                                                    @endif
                                                @endif
                                            @else
                                                @if($details[$key]->customer->type == "end_customer")
                                                    12000
                                                @else
                                                    10000
                                                @endif
                                            @endif
                                        </td>
                                        <td class="numeral total">
                                            @if($details[$key]->purchase_type)
                                                @if($details[$key]->purchase_type == "purchase")
                                                    @if($details[$key]->customer->type == "end_customer")
                                                        @if($details[$key]->is_new == "false")
                                                            {{42000*$details[$key]->additional_quantity}}
                                                        @else
                                                            {{42000*$details[$key]->order->quantity}}
                                                        @endif
                                                    @else
                                                        @if($details[$key]->is_new == "false")
                                                            {{40000*$details[$key]->additional_quantity}}
                                                        @else
                                                            {{40000*$details[$key]->order->quantity}}
                                                        @endif
                                                    @endif
                                                @elseif($details[$key]->purchase_type == "rent")
                                                    @if($details[$key]->customer->type == "end_customer")
                                                        @if($details[$key]->is_new == "false")
                                                            {{12000*$details[$key]->additional_quantity}}
                                                        @else
                                                            {{12000*$details[$key]->order->quantity}}
                                                        @endif
                                                    @else
                                                        @if($details[$key]->is_new == "false")
                                                            {{10000*$details[$key]->additional_quantity}}
                                                        @else
                                                            {{10000*$details[$key]->order->quantity}}
                                                        @endif
                                                    @endif
                                                @else
                                                    @if($details[$key]->customer->type == "end_customer")
                                                        @if($details[$key]->is_new == "false")
                                                            {{12000*$details[$key]->additional_quantity}}
                                                        @else
                                                            {{12000*$details[$key]->order->quantity}}
                                                        @endif
                                                    @else
                                                        @if($details[$key]->is_new == "false")
                                                            {{10000*$details[$key]->additional_quantity}}
                                                        @else
                                                            {{10000*$details[$key]->order->quantity}}
                                                        @endif
                                                    @endif
                                                @endif
                                            @else
                                                @if($details[$key]->customer->type == "end_customer")
                                                    {{12000*$details[$key]->order->quantity}}
                                                @else
                                                    {{10000*$details[$key]->order->quantity}}
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @if($details[$key]->is_new == "false" && $details[$key]->purchase_type && $details[$key]->order->quantity > 0)
                                        <tr>
                                            <td>{{++$i}}</td>
                                            <td>Isi ulang air</td>
                                            <td>{{$details[$key]->order->quantity}}</td>
                                            <td class="numeral">
                                                @if($details[$key]->customer->type == "end_customer")
                                                    12000
                                                @else
                                                    10000
                                                @endif
                                            </td>
                                            <td class="numeral total">
                                                @if($details[$key]->customer->type == "end_customer")
                                                    {{12000*$details[$key]->order->quantity}}
                                                @else
                                                    {{10000*$details[$key]->order->quantity}}
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-9 col-sm-9 col-xs-6 col-print-9">
                            <strong>S&K</strong>
                            <p>Terima kasih telah membeli air mineral berkualitas di ERVILL.</p>
                        </div>
                        <div class="col-lg-3 col-sm-3 col-xs-6 col-print-3 clearfix">
                            <div class="total-amount">
                                <div>Dibayar: <b class="numeral grand-total"></b></div>
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