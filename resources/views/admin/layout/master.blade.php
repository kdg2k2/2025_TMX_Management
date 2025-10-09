@extends('admin.layout.base')
@section('css')
    @include('admin.partials.styles')
    @yield('styles')
@endsection
@section('body')
    @include('admin.partials.switcher')

    @include('admin.partials.loader')

    <div class="page">
        @include('admin.partials.header')

        @include('admin.partials.sidebar')

        <div class="main-content app-content">
            <div class="container-fluid page-container main-body-container">
                @yield('content')
            </div>
        </div>

        @include('admin.partials.footer')

        @include('admin.partials.modal')

        @yield('modals')

    </div>

    <div class="scrollToTop">
        <span class="arrow lh-1"><i class="ri ri-arrow-up-fill"></i></span>
    </div>
    <div id="responsive-overlay"></div>
@endsection
@section('js')
    @include('admin.partials.scripts')

    @yield('scripts')
@endsection
