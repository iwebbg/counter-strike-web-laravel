@extends('layouts.admin')
@section('title', __('ui.servers.title'))
@section('content')
<section class="admin shell">
    @include('admin.partials.navigation')
    <div class="heading"><h1>{{ __('ui.admin.server_management') }}</h1><a class="button" href="{{ route('admin.servers.create') }}">{{ __('ui.admin.add') }}</a></div>
    @if(session('status'))<p class="flash">{{ session('status') }}</p>@endif
    <div class="panel"><table><tr><th>{{ __('ui.common.name') }}</th><th>{{ __('ui.common.game') }}</th><th>{{ __('ui.common.address') }}</th><th class="table-action"></th></tr>@foreach($servers as $server)<tr><td>{{ $server->name }}</td><td>{{ strtoupper($server->game) }}</td><td>{{ $server->address() }}</td><td class="table-action"><a href="{{ route('admin.servers.edit', $server) }}">{{ __('ui.common.edit') }}</a></td></tr>@endforeach</table></div>
</section>
@endsection
