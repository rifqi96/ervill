<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{config('app.name', 'ERVILL')}} - @yield('title')</title>

    @include('layouts.include_header')

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
</body>
</html>