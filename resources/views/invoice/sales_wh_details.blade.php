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
                    Logistik Gudang - {{$invoice->status}}
                </header>
                <div class="card-block invoice">
                    <div class="row">
                        <div class="col-lg-4 col-md-4 col-print-4 company-info">
                            <h5>ERVILL</h5>

                            <div class="invoice-block">
                                <div>Jl. Imam Bonjol No. 27E</div>
                                <div>Karawaci, Tangerang</div>
                            </div>

                            <div class="invoice-block">
                                <div>Telp: (021) 5585050</div>
                                <div>HP: 081385439665</div>
                            </div>
                        </div>
                        <div class="newhr">
                            <hr>
                        </div>
                        <div class="col-lg-4 col-md-4 col-print-4">
                            <div class="invoice-block">
                                <h5>Pemesanan untuk:</h5>
                                <div>Ibu/Bapak {{$invoice->customer->name}}</div>
                                <div>Alamat: {{$invoice->customer->address}}</div>
                                <div>No. HP: {{$invoice->customer->phone}}</div>
                            </div>
                        </div>
                        <div class="newhr">
                            <hr>
                        </div>
                        <div class="col-lg-4 col-md-4 col-print-4 clearfix invoice-info">
                            <div class="text-lg-right">
                                <h5>Nomor Faktur {{$invoice->id}}</h5>
                                <div>
                                    Tgl Pengiriman:
                                    <b class="delivery-at">{{\Carbon\Carbon::parse($invoice->delivery_at)->format('d-m-Y')}}</b>
                                </div>
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
                                </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>Galon Keluar Isi</td>
                                        <td>{{$invoice->filled_gallon}}</td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td>Galon Masuk Kosong (ERVILL)</td>
                                        <td>{{$invoice->empty_gallon}}</td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td>Galon Masuk Kosong (NON - ERVILL / MERK LAIN)</td>
                                        <td>{{$invoice->non_erv_gallon}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-9 col-sm-9 col-xs-6 col-print-9">
                            <strong>S&K</strong>
                            <p>- Harap diperhatikan bagi driver dan helper untuk membawa dan mengambil galon sesuai tabel diatas.</p>
                            <p>- Mohon untuk melapor ke bagian gudang atau administrasi jika ada masalah dalam pembawaan dan pengambilan barang.</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        $(document).ready(function () {
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