@extends('layouts.documentation')
@section('content')
    <nav id="slide-menu" class="slide-menu" role="navigation">
        <div class="brand">
            <a href="/">
                {{ config('app.name') }}
            </a>
        </div>
        <div class="slide-docs-nav">
            <h2>User Guide</h2>
            {!! $index !!}
        </div>
    </nav>
    <div class="docs-wrapper container">
        <section class="sidebar">
            {!! $index !!}
        </section>
        <article>
            {!! $content !!}
        </article>
    </div>
@endsection
