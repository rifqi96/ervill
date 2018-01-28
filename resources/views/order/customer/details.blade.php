@extends('layouts.master')

@section('title')
Detail Pesanan
@endsection

@section('content')

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <a href="{{route('order.customer.index')}}"><button class="btn btn-primary">Lihat Pesanan Customer</button></a>
                {{--<button class="btn btn-rounded btn-inline">Send</button>--}}
                <button class="btn btn-inline btn-secondary btn-rounded print">Print</button>
            </header>

            <section class="card" id="print-area">
                <header class="card-header card-header-lg">
                    Struk Pesanan
                </header>
                <div class="card-block invoice">
                    <div class="row">
                        <div class="col-lg-6 company-info">
                            <h5>ERVILL</h5>

                            <div class="invoice-block">
                                <div>Jalan Imam Bonjol</div>
                                <div>Tangerang</div>
                            </div>

                            <div class="invoice-block">
                                <div>Telephone: 555-692-7754</div>
                                <div>Fax: 555-692-7754</div>
                            </div>
                            <hr>
                            <div class="invoice-block">
                                <h5>Pemesanan untuk:</h5>
                                <div>Ibu/Bapak {{$oc->customer->name}}</div>
                                <div>Alamat: {{$oc->customer->address}}</div>
                                <div>No. HP: {{$oc->customer->phone=='0000'?"-":$oc->customer->phone}}</div>
                            </div>
                        </div>
                        <div class="col-lg-6 clearfix invoice-info">
                            <div class="text-lg-right">
                                <h5>Nomor Transaksi {{$oc->nomor_struk?$oc->nomor_struk:$oc->id}}</h5>
                                <div>Tanggal Pengiriman: <b class="delivery-at">{{\Carbon\Carbon::parse($oc->delivery_at)->format('d-m-Y')}}</b></div>
                            </div>

                            {{--<div class="payment-details">--}}
                                {{--<strong>Payment Details</strong>--}}
                                {{--<table>--}}
                                    {{--<tr>--}}
                                        {{--<td>Total Due:</td>--}}
                                        {{--<td>$8,750</td>--}}
                                    {{--</tr>--}}
                                    {{--<tr>--}}
                                        {{--<td>Bank Name:</td>--}}
                                        {{--<td>Profit Bank Europe</td>--}}
                                    {{--</tr>--}}
                                    {{--<tr>--}}
                                        {{--<td>Country:</td>--}}
                                        {{--<td>United Kingdom</td>--}}
                                    {{--</tr>--}}
                                    {{--<tr>--}}
                                        {{--<td>City:</td>--}}
                                        {{--<td>London</td>--}}
                                    {{--</tr>--}}
                                    {{--<tr>--}}
                                        {{--<td>Address:</td>--}}
                                        {{--<td>3 Goodman street</td>--}}
                                    {{--</tr>--}}
                                    {{--<tr>--}}
                                        {{--<td>IBAN:</td>--}}
                                        {{--<td>KFHT32565523921540571</td>--}}
                                    {{--</tr>--}}
                                    {{--<tr>--}}
                                        {{--<td>SWIFT Code:</td>--}}
                                        {{--<td>BPT4E</td>--}}
                                    {{--</tr>--}}
                                {{--</table>--}}
                            {{--</div>--}}
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
                                                        32000
                                                    @else
                                                        30000
                                                    @endif
                                                @elseif($details[$key]->purchase_type == "rent")
                                                    @if($details[$key]->customer->type == "end_customer")
                                                        12000
                                                    @else
                                                        10000
                                                    @endif
                                                @else
                                                    @if($details[$key]->customer->type == "end_customer")
                                                        10000
                                                    @else
                                                        8000
                                                    @endif
                                                @endif
                                            @else
                                                @if($details[$key]->customer->type == "end_customer")
                                                    9000
                                                @else
                                                    7000
                                                @endif
                                            @endif
                                        </td>
                                        <td class="numeral total">
                                            @if($details[$key]->purchase_type)
                                                @if($details[$key]->purchase_type == "purchase")
                                                    @if($details[$key]->customer->type == "end_customer")
                                                        @if($details[$key]->is_new == "false")
                                                            {{32000*$details[$key]->additional_quantity}}
                                                        @else
                                                            {{32000*$details[$key]->order->quantity}}
                                                        @endif
                                                    @else
                                                        @if($details[$key]->is_new == "false")
                                                            {{30000*$details[$key]->additional_quantity}}
                                                        @else
                                                            {{30000*$details[$key]->order->quantity}}
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
                                                            {{10000*$details[$key]->additional_quantity}}
                                                        @else
                                                            {{10000*$details[$key]->order->quantity}}
                                                        @endif
                                                    @else
                                                        @if($details[$key]->is_new == "false")
                                                            {{8000*$details[$key]->additional_quantity}}
                                                        @else
                                                            {{8000*$details[$key]->order->quantity}}
                                                        @endif
                                                    @endif
                                                @endif
                                            @else
                                                @if($details[$key]->customer->type == "end_customer")
                                                    {{9000*$details[$key]->order->quantity}}
                                                @else
                                                    {{7000*$details[$key]->order->quantity}}
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
                                                    9000
                                                @else
                                                    7000
                                                @endif
                                            </td>
                                            <td class="numeral total">
                                                @if($details[$key]->customer->type == "end_customer")
                                                    {{9000*$details[$key]->order->quantity}}
                                                @else
                                                    {{7000*$details[$key]->order->quantity}}
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
                        <div class="col-lg-9 terms-and-conditions">
                            <strong>S&K</strong>
                            <p>Terima kasih telah membeli air mineral berkualitas di ERVILL.</p>
                        </div>
                        <div class="col-lg-3 clearfix">
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

            $('.print').click(function () {
                var mywindow = window.open('', 'PRINT', 'height=400,width=600');

                mywindow.document.write('<html><head><title>ERVILL - Struk Pemesanan</title>');
                mywindow.document.write('<link rel="stylesheet" href="https://ervill.net/assets/css/lib/bootstrap/bootstrap.min.css" media="print">' +
                '<link rel="stylesheet" href="https://ervill.net/assets/css/main.css" media="print">' +
                '');
                mywindow.document.write('</head><body >');
                mywindow.document.write('<h3>ERVILL - Struk Pemesanan</h3>');
                mywindow.document.write(document.getElementById('print-area').innerHTML);
                mywindow.document.write('</body></html>');

                mywindow.document.close(); // necessary for IE >= 10
                mywindow.focus(); // necessary for IE >= 10*/

                mywindow.print();
                mywindow.close();

                return true;
            });

        });
    </script>

@endsection