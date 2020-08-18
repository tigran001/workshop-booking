@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card">
                <div class="card-header">{{ __('Add Workshop') }}</div>

                <div class="card-body">
                   <add-workshop></add-workshop>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
