@extends('layouts.app')

@section('content')
    <form action="{{ route('domain.store') }}" method="post">
        <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 col-xs-12 col-xs-offset-0">
                <label for="domain_name">Domain Name:</label>
                <input class="form-control" id="domain_name" name="domain_name" type="text" required>
            </div>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 col-xs-12 col-xs-offset-0">
                <label for="is_word_press">Is this a WordPress site?</label>
                <select class="form-control" id="is_word_press" name="is_word_press" required>
                    <option value="">Select</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 col-xs-12 col-xs-offset-0">
                <label for="is_secure">Is this a secure site?</label>
                <select class="form-control" id="is_secure" name="is_secure" required>
                    <option value="">Select</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 col-xs-12 col-xs-offset-0">
                <button type="submit" class="btn btn-success">Register Site</button>
            </div>
        </div>
    </form>
@endsection
