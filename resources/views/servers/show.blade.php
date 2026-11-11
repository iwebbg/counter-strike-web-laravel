@extends('layouts.app')
@section('title', $server->name)
@section('content')
<section class="server-hero shell">
    <div><a href="{{ route('servers.index') }}">{{ __('ui.servers.back') }}</a><p><span class="tag">{{ strtoupper($server->game) }}</span> <span class="status {{ $server->is_online ? 'on' : 'off' }}"><i></i>{{ $server->is_online ? __('ui.common.online') : __('ui.common.offline') }}</span></p><h1>{{ $server->name }}</h1><p>{{ $server->localizedDescription() }}</p><p class="buttons"><a class="button" href="steam://connect/{{ $server->address() }}">{{ __('ui.servers.connect') }}</a><button class="button ghost" data-copy="{{ $server->address() }}">{{ __('ui.servers.copy_ip') }}</button></p></div>
    <div class="map-panel"><small>{{ __('ui.common.current_map') }}</small><b>{{ $server->current_map ?? __('ui.common.unknown') }}</b><span>{{ $server->location }}</span></div>
</section>
<section class="shell telemetry">
    <div><small>{{ __('ui.common.players') }}</small><b>{{ $server->current_players }}<em>/{{ $server->max_players }}</em></b></div><div><small>{{ __('ui.common.ping') }}</small><b>{{ $server->last_ping_ms ?? '—' }}<em>ms</em></b></div><div><small>{{ __('ui.common.address') }}</small><b class="address">{{ $server->address() }}</b></div><div><small>{{ __('ui.common.updated') }}</small><b class="address">{{ $server->last_queried_at?->diffForHumans() ?? __('ui.common.no_data') }}</b></div>
</section>
<section class="shell player-ranking">
    <div class="heading"><div><span class="eyebrow">{{ __('ui.servers.scoreboard') }}</span><h2>{{ __('ui.servers.ranking') }}</h2></div><span>{{ __('ui.servers.visible', ['visible' => $players->count(), 'total' => $server->current_players]) }}</span></div>
    @if($players->isNotEmpty())
        <div class="panel"><table><thead><tr><th>#</th><th>{{ __('ui.servers.player') }}</th><th>{{ __('ui.servers.frags') }}</th><th>{{ __('ui.servers.time') }}</th></tr></thead><tbody>@foreach($players as $player)<tr><td>{{ $loop->iteration }}</td><td>{{ $player['name'] }}</td><td><strong>{{ $player['score'] }}</strong></td><td>{{ sprintf('%02d:%02d', intdiv($player['duration_seconds'], 3600), intdiv($player['duration_seconds'] % 3600, 60)) }}</td></tr>@endforeach</tbody></table></div>
        <p class="protocol-note">{{ __('ui.servers.protocol_note') }}</p>
    @else
        <div class="panel empty-players"><h3>{{ __('ui.servers.no_players') }}</h3><p>{{ __('ui.servers.no_players_help') }}</p></div>
    @endif
</section>
@endsection
