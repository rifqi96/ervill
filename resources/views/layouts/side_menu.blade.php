<ul class="side-menu-list">
    <li class="{{$module=="overview"?"opened":""}}">
        <a href="{{route('overview.index')}}">
            <i class="font-icon font-icon-home"></i>
            <span class="lbl">Overview</span>
        </a>
    </li>
    <li class="with-sub {{$module=="order"?"opened":""}}">
                <span>
                    <i class="font-icon font-icon-wallet"></i>
                    <span class="lbl">Order</span>
                </span>
        <ul>
            <li>
                {!!$slug=="gallon"?'<span class="lbl">Galon</span>':'<a href="'.route('order.gallon.index').'"><span class="lbl">Galon</span></a>'!!}
            </li>
            <li>
                {!!$slug=="water"?'<span class="lbl">Air</span>':'<a href="'.route("order.water.index").'"><span class="lbl">Air</span></a>'!!}
            </li>
            <li>
                {!!$slug=="customer"?'<span class="lbl">Customer</span>':'<a href="'.route("order.customer.index").'"><span class="lbl">Customer</span></a>'!!}
            </li>
        </ul>
    </li>
    <li class="{{$module=="shipment"?"opened":""}}">
        <a href="{{route('shipment.index')}}">
            <i class="font-icon font-icon-calend"></i>
            <span class="lbl">Pengiriman</span>
        </a>
    </li>
    <li class="with-sub {{$module=="settings"?"opened":""}}">
                <span>
                    <i class="font-icon font-icon-cogwheel"></i>
                    <span class="lbl">Pengaturan</span>
                </span>
        <ul>
            <li>
                {!!$slug=="outsourcing"?'<span class="lbl">Outsourcing</span>':'<a href="'.route("setting.outsourcing.index").'"><span class="lbl">Outsourcing</span></a>'!!}
            </li>
            <li>
                {!!$slug=="user_management"?'<span class="lbl">User Management</span>':'<a href="'.route("setting.user_management.index").'"><span class="lbl">User Management</span></a>'!!}
            </li>
            {{--<li>--}}
                {{--{!!$slug=="user_role"?'<span class="lbl">User Role</span>':'<a href="'.route("setting.user_role.index").'"><span class="lbl">User Role</span></a>'!!}--}}
            {{--</li>--}}
            {{--<li>--}}
                {{--{!!$slug=="module_access"?'<span class="lbl">Module Access</span>':'<a href="'.route("setting.module_access.index").'"><span class="lbl">Module Access</span></a>'!!}--}}
            {{--</li>--}}
        </ul>
    </li>
</ul>