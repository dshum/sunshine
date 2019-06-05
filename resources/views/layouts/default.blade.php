<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta charset="utf-8">
<title>Weather</title>
<style>
html {
    font-family: serif;
    font-size: 18px;
}
body {
    width: 30rem;
    margin: 2rem auto;
    font-size: 1rem;
}
select {
    font-size: 1rem;
}
</style>
@stack('scripts')
</head>
<body>
@yield('content')
</body>
</html>