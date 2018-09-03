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
                    <td><a href="http@if($domain->is_secure)s@endif://{{ $domain->name }}" target="_blank">{{ $domain->name }}</a></td>
                    <td class="text-center">{{ $domain->is_word_press ? 'Yes' : 'No' }}</td>
                    <td class="text-center">
                        <span class="glyphicon {{ $domain->trashed() ? 'glyphicon-ok text-success' : 'glyphicon-time text-info' }}" aria-label="{{ $domain->trashed() ? 'Completed' : 'Queued' }}"></span>
                        &nbsp; &nbsp;
                        <span class="glyphicon glyphicon-lock {{ $domain->is_secure ? 'text-success' : 'text-danger'}}" aria-label="{{ $domain->is_secure ? 'Secure Site' : 'Insecure Site' }}"></span>
                    </td>
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
