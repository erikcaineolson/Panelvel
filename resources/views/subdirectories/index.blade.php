@extends('layouts.app')

@section('pageTitle', 'Current Subdirectories')

@section('content')
    <h1>Current Subdirectories&nbsp; &nbsp; &nbsp;<a class="btn btn-success" href="{{ route('subdirectory.create') }}"><span class="glyphicon glyphicon-plus"></span></a></h1>
    <table class="table table-bordered table-responsive">
        <thead>
        <tr>
            <th>ID</th>
            <th>Subdirectory Name</th>
            <th>WordPress</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @if (isset($subdirectories))
            @foreach ($subdirectories as $subdirectory)
                <tr>
                    <td class="text-center">{{ $subdirectory->id }}</td>
                    <td><a href="http://{{ $subdirectory->name }}" target="_blank">{{ $subdirectory->name }}</a></td>
                    <td class="text-center">{{ $subdirectory->is_word_press == 1 ? 'Yes' : 'No' }}</td>
                    <td class="text-center">{{ $subdirectory->trashed() ? 'Completed' : 'Queued' }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="7">There are currently no subdirectories created.</td>
            </tr>
        @endif
        </tbody>
    </table>
@endsection
