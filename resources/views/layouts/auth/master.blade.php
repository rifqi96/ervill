<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#3273db">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{config('app.name', 'ERVILL')}} - @yield('title')</title>

    @include('layouts.auth.include_header')

</head>

<body>

<!-- Top content -->
<div class="top-content">

    <div class="inner-bg">
        <div class="container">
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2 text">
                    @include('layouts.auth.header')
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6 col-sm-offset-3 form-box">
                    @yield('content')
                </div>
            </div>
            <div class="row">
            </div>
        </div>
    </div>

</div>

</body>

</html>