@extends('admin.layout.master')
@section('content')
    <x-breadcrumb :items="[['label' => 'Dashboard', 'url' => route('dashboard')]]" />

    <div class="row">
        <div class="col-xl-12">
            <div class="card custom-card">
                <div class="card-body">
                    <h6 class="mb-0">Empty Card</h6>
                </div>
            </div>
        </div>
    </div>
@endsection
