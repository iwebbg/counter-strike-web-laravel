@extends('layouts.app')
@section('title', __('ui.admin.categories'))
@section('content')
<section class="admin shell">
    @include('admin.partials.navigation')
    <div class="heading"><h1>{{ __('ui.admin.category_management') }}</h1><a class="button" href="{{ route('admin.categories.create') }}">{{ __('ui.admin.add') }}</a></div>
    @if(session('status'))<p class="flash">{{ session('status') }}</p>@endif
    @if($errors->has('category'))<p class="error panel">{{ $errors->first('category') }}</p>@endif
    <div class="panel"><table><thead><tr><th>{{ __('ui.common.name') }}</th><th>{{ __('ui.admin.slug') }}</th><th>{{ __('ui.admin.servers') }}</th><th>{{ __('ui.admin.accent_color') }}</th><th></th></tr></thead><tbody>@foreach($categories as $category)<tr><td>{{ $category->localizedName() }}</td><td>{{ $category->slug }}</td><td>{{ $category->servers_count }}</td><td><span class="color-swatch" style="--swatch:{{ $category->accent_color }}"></span>{{ $category->accent_color }}</td><td><a href="{{ route('admin.categories.edit', $category) }}">{{ __('ui.common.edit') }}</a></td></tr>@endforeach</tbody></table></div>
</section>
@endsection
