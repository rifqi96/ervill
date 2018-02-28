<ul class="side-menu-list">
    <li class="{{$module=="overview"?"opened":""}}">
        <a href="{{route('overview.index')}}">
            <i class="font-icon font-icon-home"></i>
            <span class="lbl">Overview</span>
        </a>
    </li>
    <li class="with-sub {{$module=="order"?"opened":""}}">
        <span>
            <i class="font-icon font-icon-cart-2"></i>
            <span class="lbl">Pesanan</span>
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
            <li>
                {!!$slug=="customerNonErvill"?'<span class="lbl">Customer Non Ervill</span>':'<a href="'.route("order.customerNonErvill.index").'"><span class="lbl">Customer Non Ervill</span></a>'!!}
            </li>
        </ul>
    </li>
    <li class="{{$module=="shipment"?"opened":""}}">
        <a href="{{route('shipment.index')}}">
            <i class="font-icon font-icon-calend"></i>
            <span class="lbl">Pengiriman</span>
        </a>
    </li>
    <li class="{{$module=="return"?"opened":""}}">
        <a href="{{route('return.index')}}">
            <i class="font-icon font-icon-import"></i>
            <span class="lbl">Retur Galon</span>
        </a>
    </li>
    <li class="with-sub {{$module=="invoice"?"opened":""}}">
        <span>
            <i class="font-icon font-icon-card"></i>
            <span class="lbl">
                Faktur
            </span>
        </span>
        <ul>
            <li>
                {!!$module=="invoice" && $slug=="sales"?'<span class="lbl">Penjualan</span>':'<a href="'.route('invoice.sales.index').'"><span class="lbl">Penjualan</span></a>'!!}
            </li>
            <li>
                {!!$module=="invoice" && $slug=="return"?'<span class="lbl">Retur</span>':'<a href="'.route("invoice.return.index").'"><span class="lbl">Retur</span></a>'!!}
            </li>
        </ul>
    </li>
    <li class="with-sub {{$module=="report"?"opened":""}}">
        <span>
            <i class="font-icon font-icon-list-square"></i>
            <span class="lbl">
                Laporan
            </span>
        </span>
        <ul>
            <li>
                {!!$module=="report" && $slug=="sales"?'<span class="lbl">Penjualan</span>':'<a href="'.route('report.sales.index').'"><span class="lbl">Penjualan</span></a>'!!}
            </li>
            <li>
                {!!$module=="report" && $slug=="income"?'<span class="lbl">Penerimaan</span>':'<a href="'.route('report.income.index').'"><span class="lbl">Penerimaan</span></a>'!!}
            </li>
        </ul>
    </li>
    <li class="{{$module=="inventory"?"opened":""}}">
        <a href="{{route('inventory.index')}}">
            <i class="font-icon font-icon-archive"></i>
            <span class="lbl">Inventori</span>
        </a>
    </li>
    <li class="with-sub {{$module=="customers"?"opened":""}}">
        <span>
            <i class="font-icon font-icon-users"></i>
            <span class="lbl">
                Customer
            </span>
        </span>
        <ul>
            <li>
                {!!$slug=="list"?'<span class="lbl">Daftar Customer</span>':'<a href="'.route('setting.customers.index').'"><span class="lbl">Daftar Customer</span></a>'!!}
            </li>
            <li>
                {!!$slug=="listNonErvill"?'<span class="lbl">Daftar Customer Pihak Ketiga</span>':'<a href="'.route('setting.customerNonErvills.index').'"><span class="lbl">Daftar Customer Pihak Ketiga</span></a>'!!}
            </li>
            <li>
                {!!$slug=="overdue"?'<span class="lbl">Customer Overdue</span>':'<a href="'.route("setting.customers.overdue").'"><span class="lbl">Customer Overdue</span></a>'!!}
            </li>
        </ul>
    </li>
    @if(auth()->user()->role->name == 'superadmin')
    <li class="with-sub {{$module=="history"?"opened":""}}">
        <span>
            <i class="font-icon font-icon-clock"></i>
            <span class="lbl">History</span>
        </span>
        <ul>
            <li>
                {!!$slug=="edit_history"?'<span class="lbl">Edit History</span>':'<a href="'.route("history.edit.index").'"><span class="lbl">Edit History</span></a>'!!}
            </li>
            <li>
                {!!$slug=="delete_history"?'<span class="lbl">Delete History</span>':'<a href="'.route("history.delete.index").'"><span class="lbl">Delete History</span></a>'!!}
            </li>
        </ul>
    </li>
    @endif
    <li class="with-sub {{$module=="settings"?"opened":""}}">
                <span>
                    <i class="font-icon font-icon-cogwheel"></i>
                    <span class="lbl">Pengaturan</span>
                </span>
        <ul>
            {{--<li>--}}
                {{--{!!$slug=="customers"?'<span class="lbl">Customer</span>':'<a href="'.route("setting.customers.index").'"><span class="lbl">Customer</span></a>'!!}--}}
            {{--</li>--}}
            <li>
                {!!$slug=="price"?'<span class="lbl">Daftar Harga</span>':'<a href="'.route("price.index").'"><span class="lbl">Daftar Harga</span></a>'!!}
            </li>
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