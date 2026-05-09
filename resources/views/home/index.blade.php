@extends('home.layouts.app')
@section('content')

    {{-- Hero is now included in the layout's navigation partial --}}

    {{-- About: trust + story --}}
    <section data-reveal>
        @include('home.sections.about')
    </section>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Westin Hotels Reclone</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="antialiased bg-gray-50">


</body>
</html>

    {{-- All rooms: premium card grid --}}
    <section data-reveal>
        @include('home.sections.room', ['showPromos' => false])
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
