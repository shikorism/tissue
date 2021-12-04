@extends('layouts.base')

@section('content')
    @component('user.components.header', ['user' => $user])
    @endcomponent
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                @section('sidebar')
                @show
            </div>
            <div class="col-lg-8">
                @yield('tab-content')
            </div>
        </div>
    </div>
@endsection
