@extends('layouts.app')
@section('title', $post->localized('title'))
@section('content')
<article class="article shell"><a href="{{ route('news.index') }}">{{ __('ui.news.all') }}</a><time>{{ $post->published_at->translatedFormat('d.m.Y · H:i') }}</time><h1>{{ $post->localized('title') }}</h1><p class="lead">{{ $post->localized('excerpt') }}</p><div>{{ $post->localizedBodyHtml() }}</div></article>
@endsection
