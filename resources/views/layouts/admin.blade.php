@extends('admin.layouts.app', [
    'title' => $title ?? 'Admin',
    'active' => $active ?? 'dashboard',
])

@section('content')
    @yield('content')
@endsection
