@extends('admin.layout.base')
@section('css')
    @include('admin.partials.styles')
    @yield('styles')
@endsection
@section('body')
    @include('admin.partials.process-top-bar')
    @include('admin.partials.switcher')

    <div class="page">
        @include('admin.partials.header')
        @include('admin.partials.sidebar')

        <div class="main-content app-content">
            <div class="container-fluid page-container main-body-container">
                @yield('content')
            </div>
        </div>

        @include('admin.partials.footer')
        @include('admin.partials.modals')
        @yield('modals')
    </div>

    @include('admin.partials.scroll-to-top')
    <div id="responsive-overlay"></div>
@endsection
@section('js')
    @include('admin.partials.scripts')
    @yield('scripts')
@endsection
