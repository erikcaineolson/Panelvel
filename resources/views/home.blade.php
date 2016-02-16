@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    <ul>
                        <li class="h3"><a href="{{ url('domain.create') }}">Create New Site</a></li>
                        <li class="h3"><a href="{{ url('domain.index') }}">View Existing Sites</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
