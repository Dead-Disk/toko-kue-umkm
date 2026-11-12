@extends('staff.layouts.app', [
    'title' => $title ?? 'Staf',
    'active' => $active ?? 'dashboard',
])

@section('content')
    @yield('content')
@endsection
