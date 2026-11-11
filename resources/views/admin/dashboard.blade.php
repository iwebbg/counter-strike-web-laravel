@extends('layouts.app')
@section('title', __('ui.admin.dashboard'))
@section('content')
<section class="admin shell">
    @include('admin.partials.navigation')
    <div class="heading"><h1>{{ __('ui.admin.dashboard') }}</h1><p class="buttons"><a class="button" href="{{ route('admin.servers.create') }}">{{ __('ui.admin.add_server') }}</a></p></div>
    <div class="metrics"><div><small>{{ __('ui.admin.servers') }}</small><b>{{ $serverCount }}</b></div><div><small>{{ __('ui.common.online') }}</small><b>{{ $onlineCount }}</b></div><div><small>{{ __('ui.admin.players') }}</small><b>{{ $playerCount }}</b></div><div><small>{{ __('ui.admin.news') }}</small><b>{{ $newsCount }}</b></div></div>
    <div class="panel"><div class="heading"><h2>{{ __('ui.admin.latest_status') }}</h2><a href="{{ route('admin.servers.index') }}">{{ __('ui.admin.management') }}</a></div><table><tr><th>{{ __('ui.nav.servers') }}</th><th>{{ __('ui.common.status') }}</th><th>{{ __('ui.common.map') }}</th><th>{{ __('ui.common.players') }}</th></tr>@foreach($servers as $server)<tr><td><a href="{{ route('admin.servers.edit', $server) }}">{{ $server->name }}</a></td><td>{{ $server->is_online ? __('ui.common.online') : __('ui.common.offline') }}</td><td>{{ $server->current_map }}</td><td>{{ $server->current_players }}/{{ $server->max_players }}</td></tr>@endforeach</table></div>
</section>
@endsection
