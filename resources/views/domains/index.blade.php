@extends('layouts.app')

@section('pageTitle', 'Current Domains')

@section('content')
    <h1>Current Domains&nbsp; &nbsp; &nbsp;<a class="btn btn-success" href="{{ route('domain.create') }}"><span class="glyphicon glyphicon-plus"></span></a></h1>
    <table class="table table-bordered table-responsive">
        <thead>
        <tr>
            <th>ID</th>
            <th>Domain Name</th>
            <th>WordPress</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @if (isset($domains))
            @foreach ($domains as $domain)
                <tr>
                    <td class="text-center">{{ $domain->id }}</td>
                    <td><a href="http://{{ $domain->name }}" target="_blank">{{ $domain->name }}</a></td>
                    <td class="text-center">{{ $domain->is_word_press == 1 ? 'Yes' : 'No' }}</td>
                    <td class="text-center">{{ $domain->trashed() ? 'Completed' : 'Queued' }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="7">There are currently no domains created.</td>
            </tr>
        @endif
        </tbody>
    </table>
@endsection