@extends('layouts.admin')
@section('title', __('ui.admin.news'))
@section('content')
<section class="admin shell">
    @include('admin.partials.navigation')
    <div class="heading"><h1>{{ __('ui.admin.news_management') }}</h1><a class="button" href="{{ route('admin.news.create') }}">{{ __('ui.admin.add') }}</a></div>
    @if(session('status'))<p class="flash">{{ session('status') }}</p>@endif
    <div class="panel"><table><thead><tr><th>{{ __('ui.admin.title') }}</th><th>{{ __('ui.admin.publication_date') }}</th><th>{{ __('ui.common.status') }}</th><th class="table-action"></th></tr></thead><tbody>@foreach($posts as $post)<tr><td>{{ $post->localized('title') }}</td><td>{{ $post->published_at?->translatedFormat('d.m.Y H:i') ?? '—' }}</td><td>{{ $post->is_published ? __('ui.admin.published') : __('ui.admin.draft') }}</td><td class="table-action"><a href="{{ route('admin.news.edit', $post) }}">{{ __('ui.common.edit') }}</a></td></tr>@endforeach</tbody></table></div>
    {{ $posts->links('vendor.pagination.fragzone') }}
</section>
@endsection
