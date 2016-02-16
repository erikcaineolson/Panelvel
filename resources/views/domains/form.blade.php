@extends('layouts.app')

@section('content')
    <form action="{{ route('domain.store') }}" method="post">
        <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}">
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 col-xs-12 col-xs-offset-0">
                <label for="domain_name">Domain Name:</label>
                <input class="form-control" id="domain_name" name="domain_name" type="text">
            </div>
        </div>
        <div class="row">&nbsp;</div>
        <div class="row">
            <div class="col-sm-6 col-sm-offset-3 col-xs-12 col-xs-offset-0">
                <input id="is_word_press" name="is_word_press" type="checkbox" value="1">&nbsp; <label for="is_word_press">Is this a WordPress site?</label>
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