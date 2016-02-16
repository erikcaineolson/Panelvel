<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ isset($pageTitle) ? $pageTitle : 'Site Generator' }}</title>
    <link rel="stylesheet" type="text/css">
</head>
<body>
    <div class="container">
        <form action="{{ route('user.login') }}" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="row">
                <div class="col-md-4 col-sm-3 col-xs-0"></div>
                <div class="col-md-4 col-sm-6 col-xs-12">
                    <label for="username">Username:</label>
                    <input class="form-control" type="text" id="username" name="username">
                    <label for="password">Password:</label>
                    <input class="form-control" type="password" id="password" name="password">
                </div>
                <div class="col-md-4 col-sm-3 col-xs-0"></div>
            </div>
        </form>
    </div>
</body>
</html>