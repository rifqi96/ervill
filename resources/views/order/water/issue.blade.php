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

                <form action="" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Nama Pengemudi</label>
                        <div class="col-sm-10">
                            <p class="form-control-static"><input type="text" class="form-control" name="driver_name" placeholder="Nama Pengemudi"></p>
                           
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-2 form-control-label">Tipe Masalah</label>
                        <div class="col-sm-10">
                            <p class="form-control-static">                                               
                                <label class="checkbox-inline" style="display: inline-block;">
                                  <input type="checkbox" id="inlineCheckbox1" value="option1"> Tipe 1
                                </label>
                                <label class="checkbox-inline" style="display: inline-block;">
                                  <input type="checkbox" id="inlineCheckbox2" value="option2"> Tipe 2
                                </label>
                                <label class="checkbox-inline" style="display: inline-block;">
                                  <input type="checkbox" id="inlineCheckbox3" value="option3"> Tipe 3
                                </label>
                            </p>
                        </div>
                    </div> 

                    <hr> 
                    
                    <div id="type1" style="display: none;">
                        <p><strong>Tipe 1</strong></p>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Jumlah Galon yang Bermasalah</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><input type="number" class="form-control" name="quantity" placeholder="Jumlah Galon yang Bermasalah"></p>
                               
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Deskripsi Masalah</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><textarea class="form-control" name="description" placeholder="Deskripsi Masalah" rows="5"></textarea></p>
                               
                            </div>
                        </div>   
                    </div>         

                    <div id="type2" style="display: none;">
                        <p><strong>Tipe 2</strong></p>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Jumlah Segel yang Bermasalah</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><input type="number" class="form-control" name="quantity" placeholder="Jumlah Segel yang Bermasalah"></p>
                               
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Deskripsi Masalah</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><textarea class="form-control" name="description" placeholder="Deskripsi Masalah" rows="5"></textarea></p>
                               
                            </div>
                        </div>   
                    </div> 

                    <div id="type3" style="display: none;">
                        <p><strong>Tipe 3</strong></p>
                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Jumlah Tisu yang Bermasalah</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><input type="number" class="form-control" name="quantity" placeholder="Jumlah Tisu yang Bermasalah"></p>
                               
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 form-control-label">Deskripsi Masalah</label>
                            <div class="col-sm-10">
                                <p class="form-control-static"><textarea class="form-control" name="description" placeholder="Deskripsi Masalah" rows="5"></textarea></p>
                               
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
            });
            $("#inlineCheckbox2").change(function() {
                if(this.checked)
                    $('#type2').css('display','block');
                else
                    $('#type2').css('display','none');
            });
            $("#inlineCheckbox3").change(function() {
                if(this.checked)
                    $('#type3').css('display','block');
                else
                    $('#type3').css('display','none');
            });
        });
    </script>

@endsection