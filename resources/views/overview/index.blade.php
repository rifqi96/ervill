@extends('layouts.master')

@section('title')
Overview
@endsection

@section('content')
    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading">
                <h3 class="panel-title">Recent Order</h3>
            </header>
            <table class="table table-hover" id="recent_order">
                <thead>
                <th>Status</th>
                <th>Customer Name</th>
                <th>Quantity</th>
                <th align="center">Date</th>
                <th>Action</th>
                </thead>
                <tbody>
                <tr>
                    <td><span class="label label-warning">Process</span></td>
                    <td>Abi</td>
                    <td>5</td>
                    <td>1 Hour Ago</td>
                    <td><button class="btn btn-sm">Live Tracking</button></td>
                </tr>
                <tr>
                    <td><span class="label label-success">Completed</span></td>
                    <td>Safira</td>
                    <td>2</td>
                    <td>2 Hours Ago</td>
                    <td><button class="btn btn-sm">Tracking History</button></td>
                </tr>
                <tr>
                    <td><span class="label label-danger">Issue</span></td>
                    <td>Hasbur</td>
                    <td>3</td>
                    <td>5 Hours Ago</td>
                    <td><button class="btn btn-sm">Tracking History</button></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-12 dashboard-column">
            <header class="box-typical-header panel-heading">
                <h3 class="panel-title">Recent Issue</h3>
            </header>
            <table class="table table-hover" id="recent_issue">
                <thead>
                <th>Status</th>
                <th>Customer Name</th>
                <th>Quantity</th>
                <th>Driver Name</th>
                <th align="center">Date</th>
                <th>Action</th>
                </thead>
                <tbody>
                <tr>
                    <td><span class="label label-danger">Replace</span></td>
                    <td>Abi</td>
                    <td>1</td>
                    <td>Saipul</td>
                    <td>1 Hour Ago</td>
                    <td><button class="btn btn-sm">See Details</button></td>
                </tr>
                <tr>
                    <td><span class="label label-danger">Refund</span></td>
                    <td>Sabrina</td>
                    <td>2</td>
                    <td>Saipul</td>
                    <td>1 Hour Ago</td>
                    <td><button class="btn btn-sm">See Details</button></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        $(document).ready(function () {
           $('#recent_order').dataTable({
               'order':[3, 'asc']
           });

            $('#recent_issue').dataTable({
                'order':[3, 'asc']
            });
        });
    </script>
@endsection