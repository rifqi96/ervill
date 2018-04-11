<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="theme-color" content="#3273db">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{config('app.name', 'ERVILL')}} - @yield('title')</title>

    @include('layouts.include_header')

    <style>
        .select2-container--classic .select2-results__option--highlighted[aria-selected] {
            background: #f00;
            color: #3875d7;
        }
    </style>

    <script>
        nprogress.configure({ minimum: 0.2, easing: 'linear', showSpinner:false, trickleSpeed: 100 });

        $(document)
            .ajaxStart(nprogress.start)
            .ajaxStop(nprogress.done);
    </script>

</head>
<body class="with-side-menu control-panel control-panel-compact">

<header class="site-header">
    <div class="container-fluid">
        @include('layouts.header')
    </div><!--.container-fluid-->
</header><!--.site-header-->

<div class="mobile-menu-left-overlay"></div>

<nav class="side-menu side-menu-big-icon">
    @include('layouts.side_menu')
</nav><!--.side-menu-->

<div class="page-content">
    <div class="container-fluid">
        @include('layouts.content_header')<!--title and breadcrumb-->

        @yield('content')
    </div><!--.container-fluid-->
</div><!--.page-content-->
@include('layouts.errors')<!--.errors-->
@include('layouts.success')<!--.errors-->

<script type="text/javascript">
    // load a locale
    numeral.register('locale', 'id', {
        delimiters: {
            thousands: '.',
            decimal: ','
        },
        abbreviations: {
            thousand: 'k',
            million: 'juta',
            billion: 'milyar',
            trillion: 'triliun'
        },
        ordinal : function (number) {
            return number === 1 ? 'er' : 'ème';
        },
        currency: {
            symbol: 'Rp'
        }
    });

    // switch between locales
    numeral.locale('id');
    //disable button after submit
    $("form").submit(function(){
        $(this).find("button[type='submit'],input[type='submit']").attr('disabled','disabled');
    });
    $(".ajax-btn").click(function(){
        $(this).attr('disabled','disabled');
    });
    //disable scrolling feature in input type number
    $('form').on('focus', 'input[type=number]', function (e) {
      $(this).on('mousewheel.disableScroll', function (e) {
        e.preventDefault()
      });
    });
    $('form').on('blur', 'input[type=number]', function (e) {
      $(this).off('mousewheel.disableScroll');
    });

    $(document).ready(function () {
        $('.select2').select2({
            theme:'classic'
        });
        //nprogress.start();

    });
</script>
</body>
</html>