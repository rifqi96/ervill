<ul class="side-menu-list">
    <li class="{{$module=="overview"?"opened":""}}">
        <a href="">
            <i class="font-icon font-icon-dashboard"></i>
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
                {!!$slug=="gallon"?'<span class="lbl">Gallon</span>':'<a href=""><span class="lbl">Gallon</span></a>'!!}
            </li>
            <li>
                {!!$slug=="water"?'<span class="lbl">Water</span>':'<a href=""><span class="lbl">Water</span></a>'!!}
            </li>
            <li>
                {!!$slug=="customer"?'<span class="lbl">Customer</span>':'<a href=""><span class="lbl">Customer</span></a>'!!}
            </li>
        </ul>
    </li>
    <li class="with-sub {{$module=="settings"?"opened":""}}">
                <span>
                    <i class="font-icon font-icon-build"></i>
                    <span class="lbl">Settings</span>
                </span>
        <ul>
            <li>
                {!!$slug=="outsourcing"?'<span class="lbl">Outsourcing</span>':'<a href=""><span class="lbl">Outsourcing</span></a>'!!}
            </li>
            <li>
                {!!$slug=="user_management"?'<span class="lbl">User Management</span>':'<a href=""><span class="lbl">User Management</span></a>'!!}
            </li>
            <li>
                {!!$slug=="user_role"?'<span class="lbl">User Role</span>':'<a href=""><span class="lbl">User Role</span></a>'!!}
            </li>
            <li>
                {!!$slug=="module_access"?'<span class="lbl">Module Access</span>':'<a href=""><span class="lbl">Module Access</span></a>'!!}
            </li>
        </ul>
    </li>
</ul>