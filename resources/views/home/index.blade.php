@extends('home.layouts.app')
@section('content')

    {{-- Hero is included in nav --}}

    {{-- About: trust + story --}}
    <section>
        @include('home.sections.about')
    </section>

    {{-- All rooms: premium card grid --}}
    <section data-reveal>
        @include('home.sections.room', ['showPromos' => true])
    </section>

    {{-- Testimonials --}}
    <section data-reveal>
        @include('home.sections.testimonials')
    </section>

    {{-- Gallery --}}
    <section data-reveal>
        @include('home.sections.gallery')
    </section>

@endsection
