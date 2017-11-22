@extends('layouts.master')

@section('title')
Order Water Issue
@endsection

@section('content')

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading" style="margin-bottom: 30px;">
                <!--<h3 class="panel-title">Isu Air</h3>-->
                <a href="{{route('order.water.index')}}"><button class="btn btn-primary">Lihat Pesanan Air</button></a>   
            </header>

            <section class="box-typical box-typical-padding">              

                <form action="{{route('order.water.do.makeIssue')}}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <input type="hidden" name="id" value="{{$orderWater->id}}">
                    <input type="hidden" name="order_id" value="{{$orderWater->order->id}}">
                    <input type="hidden" name="max_quantity" value="{{$orderWater->order->quantity}}">

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Nama Pengemudi</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><input type="text" class="form-control" name="driver_name" placeholder="Nama Pengemudi"></p>
                           
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Jenis Barang yang Bermasalah</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">                                               
                                <label class="checkbox-inline" style="display: inline-block;">
                                  <input type="checkbox" id="inlineCheckbox1" value="Galon Rusak" name="typeGallon"> Galon Rusak
                                </label>
                                <label class="checkbox-inline" style="display: inline-block;">
                                  <input type="checkbox" id="inlineCheckbox2" value="Segel Rusak" name="typeSeal"> Segel Rusak
                                </label>
                                <label class="checkbox-inline" style="display: inline-block;">
                                  <input type="checkbox" id="inlineCheckbox3" value="Tisu Kurang" name="typeTissue"> Tisu Kurang
                                </label>
                            </p>
                        </div>
                    </div> 

                    <hr> 
                    
                    <div id="type1" style="display: none;">
                        <p><strong>Galon Rusak</strong></p>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Tipe Masalah</label>
                            <div class="col-sm-10">
                                <select name="type" class="form-control">
                                    <option value=""></option>
                                    <option value="Kesalahan Pabrik Air">Kesalahan Pabrik Air</option>                      
                                </select>                                                     
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Jumlah Galon yang Bermasalah</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><input type="number" class="form-control" name="quantity_gallon" placeholder="Jumlah Galon yang Bermasalah" max="{{$orderWater->order->quantity}}"></p>
                               
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Deskripsi Masalah</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><textarea class="form-control" name="description_gallon" placeholder="Deskripsi Masalah" rows="5">Galon rusak</textarea></p>
                               
                            </div>
                        </div>   
                    </div>         

                    <div id="type2" style="display: none;">
                        <p><strong>Segel Rusak</strong></p>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Jumlah Segel yang Bermasalah</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><input type="number" class="form-control" name="quantity_seal" placeholder="Jumlah Segel yang Bermasalah"></p>
                               
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Deskripsi Masalah</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><textarea class="form-control" name="description_seal" placeholder="Deskripsi Masalah" rows="5">Segel rusak</textarea></p>
                               
                            </div>
                        </div>   
                    </div> 

                    <div id="type3" style="display: none;">
                        <p><strong>Tisu Kurang</strong></p>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Jumlah Tisu yang Bermasalah</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><input type="number" class="form-control" name="quantity_tissue" placeholder="Jumlah Tisu yang Bermasalah"></p>
                               
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Deskripsi Masalah</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><textarea class="form-control" name="description_tissue" placeholder="Deskripsi Masalah" rows="5">Tisu kurang</textarea></p>
                               
                            </div>
                        </div>   
                    </div>    
                                        
                    <div class="form-group row">
                        <div class="col-sm-2"></div>
                        <div class="col-sm-10">
                            <input type="submit" value="Submit" class="btn">
                            <input type="reset" value="Reset" class="btn btn-info">
                        </div>
                    </div>
                </form>
            </section><!--.box-typical-->
            
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            $("#inlineCheckbox1").change(function() {
                if(this.checked)
                    $('#type1').css('display','block');
                else
                    $('#type1').css('display','none');

                $('#type1 input').val('');
            });
            $("#inlineCheckbox2").change(function() {
                if(this.checked)
                    $('#type2').css('display','block');
                else
                    $('#type2').css('display','none');

                $('#type2 input').val('');
            });
            $("#inlineCheckbox3").change(function() {
                if(this.checked)
                    $('#type3').css('display','block');
                else
                    $('#type3').css('display','none');

                $('#type3 input').val('');
            });
        });
    </script>

@endsection