@extends('layouts.app')
@section('title') Convert @stop
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8 bg-light min-vh-100">
            <div class="text-center" style="min-height: 300px;">
                <convert
                    @if (!empty($convertRequest))
                        :convert-request-raw="{{ $convertRequest }}"
                    @endif

                    @if (!empty($convertRequestItem))
                        :convert-request-item-raw="{{ $convertRequestItem }}"
                    @endif
                ></convert>
            </div>
        </div>
    </div>
</div>
@stop