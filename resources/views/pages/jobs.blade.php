@extends('layouts.app')

@section('content')
    <div class="page-hero">
        <div class="container">
            <h1 class="page-hero__title">{{ $page->title() }}</h1>
        </div>
    </div>

    <x-coming-soon-section label="Open Positions" />
    <x-coming-soon-section label="Benefits" />
    <x-coming-soon-section label="How to Apply" />
@endsection
