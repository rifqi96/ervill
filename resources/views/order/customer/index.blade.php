@extends('layouts.master')

@section('title')
List Pesanan Customer
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <!--<h3 class="panel-title">Pesanan Air</h3>-->
                <a href="{{route('order.customer.make')}}"><button class="btn btn-primary">Pesan Customer</button></a>               
            </header>

            <table class="table table-hover" id="customer_order">
                <thead>
                <th>Status</th>
                <th>ID</th>
                <th>Admin</th>
                <th>Nama Pengemudi</th>
                <th>Customer</th>
                <th>Alamat Customer</th>
                <th>Jumlah (Galon)</th> 
                <th>Jumlah Galon Kosong (Galon)</th>                
                <th>Tgl Order</th>
                <th>Tgl Pengiriman</th>
                <th>Tgl Penerimaan</th>
                <th>Action</th>
                </thead>
                <tbody>
                <tr>
                    <td><span class="label label-success">Selesai</span></td>
                    <td>1</td>
                    <td>Abi</td>
                    <td>Ervill Driver 1</td>
                    <td>Customer 1</td>
                    <td>Cimone Raya blok C4 nomor 4, Cimone, Tangerang</td>
                    <td>3</td>
                    <td>3</td>
                    <td>20/10/2017 08:20:55</td>
                    <td>20/10/2017</td>
                    <td>20/10/2017 12:20:55</td>
                    <td>                                     
                        <a class="btn btn-sm" href="{{route('order.customer.track')}}">Tracking History</a>       	
                    	<button class="btn btn-sm">Edit</button>
                    	<button class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
                <tr>
                    <td><span class="label label-danger">Bermasalah</span></td>
                    <td>2</td>
                    <td>Abi</td>
                    <td>Ervill Driver 1</td>
                    <td>Customer 2</td>
                    <td>Serpong Paradise blok G4 nomor 43, Serpong, Tangerang</td>
                    <td>4</td>
                    <td>4</td>
                    <td>20/10/2017 09:20:55</td>
                    <td>20/10/2017</td>
                    <td>20/10/2017 12:20:55</td>
                    <td>                                      
                        <a class="btn btn-sm" href="{{route('order.customer.track')}}">Tracking History</a>  
                        <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#issueModal">Lihat Masalah</button> 
                        <button class="btn btn-sm">Edit</button>
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
               <tr>
                    <td><span class="label label-warning">Proses</span></td>
                    <td>3</td>
                    <td>Charlie</td>
                    <td>Ervill Driver 2</td>
                    <td>Customer 2</td>
                    <td>Serpong Paradise blok G4 nomor 43, Serpong, Tangerang</td>
                    <td>4</td>
                    <td>0</td>
                    <td>20/10/2017 11:25:32</td>
                    <td>20/10/2017</td>
                    <td>-</td>
                    <td>                                     
                        <a class="btn btn-sm" href="{{route('order.customer.track')}}">Live Tracking</a>  
                        <button class="btn btn-sm">Edit</button>
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
                </tbody>
            </table>




            
        </div>
    </div>


    <!-- Issue Modal -->

    <div class="modal fade" id="issueModal" tabindex="-1" role="dialog" aria-labelledby="issueModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">

          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="issueModalLabel">Detail Masalah</h4>
          </div>
     
              <div class="modal-body">                       
                <div class="form-group">
                    <label for="description"><strong>Deskripsi Masalah</strong></label>
                    <p class="form-control-static">
                        Galon pecah 1 karena terjatuh
                    </p> 
                </div> 
                <div class="form-group">
                    <label for="quantity"><strong>Jumlah Galon yang Bermasalah</strong></label>
                    <p class="form-control-static">
                        1
                    </p> 
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
            $('#customer_order').dataTable({
                scrollX: true,  
                fixedHeader: true,       
                processing: true,
                'order':[8, 'desc']
            });
        });
    </script>

@endsection