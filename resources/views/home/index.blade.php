@extends('home.layouts.app')
@section('content')

    @include('home.partials.chatbot')

    {{-- Hero: cinematic carousel + search bar --}}
    @include('home.sections.hero')

    {{-- About: trust + story --}}
    <section data-reveal>
        @include('home.sections.about')
    </section>

    {{-- All rooms: premium card grid --}}
    <section data-reveal>
        @include('home.sections.room', ['showPromos' => false])
    </section>

    {{-- Testimonials --}}
    <section data-reveal>
        @include('home.sections.testimonials')
    </section>

@endsection
