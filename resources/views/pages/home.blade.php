@extends('layouts.app')

@section('content')
    <div class="page-hero">
        <div class="container">
            <h1 class="page-hero__title">{{ $page->title() }}</h1>
        </div>
    </div>

    <x-coming-soon-section label="Hero" />
    <x-coming-soon-section label="Highlights" />
    <x-coming-soon-section label="Latest News" />
@endsection
