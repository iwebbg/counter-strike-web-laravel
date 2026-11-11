@extends('layouts.app')
@section('title', __('ui.news.title'))
@section('content')
<section class="page-hero shell"><span class="eyebrow">{{ __('ui.news.eyebrow') }}</span><h1>{{ __('ui.news.heading') }} <em>{{ __('ui.news.events') }}</em></h1></section>
<section class="shell news-list">@foreach($posts as $post)<article><time>{{ $post->published_at->format('d.m.Y') }}</time><div><h2><a href="{{ route('news.show', $post) }}">{{ $post->title }}</a></h2><p>{{ $post->excerpt }}</p></div><a href="{{ route('news.show', $post) }}">→</a></article>@endforeach</section>
@endsection
