@extends('layouts.app')
@section('title', $post->title)
@section('content')
<article class="article shell"><a href="{{ route('news.index') }}">{{ __('ui.news.all') }}</a><time>{{ $post->published_at->translatedFormat('d.m.Y · H:i') }}</time><h1>{{ $post->title }}</h1><p class="lead">{{ $post->excerpt }}</p><div>{!! nl2br(e($post->body)) !!}</div></article>
@endsection
