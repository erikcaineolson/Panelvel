@extends('layouts.app')

@section('content')
    <form action="{{ route('domain.store') }}" method="post">
        <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 col-xs-12 col-xs-offset-0">
                <label for="domain_id">Which Domain is this For?</label>
                <select class="form-control" id="domain_id" name="domain_id" required>
                    <option value="">Select</option>
                    @foreach($domains as $domain)
                        <option value="{{ $domain->id }}">{{ $domain->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 col-xs-12 col-xs-offset-0">
                <label for="subdirectory_name">Subdirectory Name:</label>
                <input class="form-control" id="subdirectory_name" name="domain_name" type="text" required>
            </div>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 col-xs-12 col-xs-offset-0">
                <input id="is_word_press" name="is_word_press" type="checkbox" value="1">
                <label for="is_word_press">Install WordPress</label>
            </div>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 col-xs-12 col-xs-offset-0">
                <button type="submit" class="btn btn-success">Create Subdirectory</button>
            </div>
        </div>
    </form>
@endsection
