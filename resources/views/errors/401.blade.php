@extends('admin.layout.base')

@php
    $bodyClass = '';
@endphp

@section('body')
    <div class="page error-bg">
        <div class="error-page-background">
            <img src="assets/images/media/backgrounds/10.svg" alt="">
        </div>
        <!-- Start::error-page -->
        <div class="row align-items-center justify-content-center h-100 g-0">
            <div class="col-xl-7 col-lg-7 col-md-7 col-12">
                <div class="text-center px-2">
                    <span class="d-block fs-4 text-primary fw-semibold">Oops! Something Went Wrong</span>
                    <p class="error-text mb-0">401</p>
                    <p class="fs-5 fw-normal mb-0">There was an issue with the page. Try again <br> later or contact
                        support.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
