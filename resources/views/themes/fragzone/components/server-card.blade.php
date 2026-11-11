<article @class(['server-card', 'is-featured' => $server->is_featured])>
    <div class="card-top"><div class="card-badges"><span class="tag">{{ $server->game === 'cs16' ? 'CS 1.6' : 'CS:GO' }}</span>@if($server->is_featured)<span class="featured-tag">★ {{ __('ui.servers.featured') }}</span>@endif</div><span class="status {{ $server->is_online ? 'on' : 'off' }}"><i></i>{{ $server->is_online ? __('ui.common.online') : __('ui.common.offline') }}</span></div>
    <div class="map"><small>{{ __('ui.common.current_map') }}</small><b>{{ strtoupper($server->current_map ?? __('ui.common.unknown')) }}</b><span>{{ $server->category->localizedName() }}</span></div>
    <div class="card-body">
        <h3><a href="{{ route('servers.show', $server) }}">{{ $server->name }}</a></h3>
        <button class="copy" data-copy="{{ $server->address() }}">{{ $server->address() }} ⧉</button>
        <div class="bar"><i style="width:{{ $server->max_players ? min(100, round($server->current_players / $server->max_players * 100)) : 0 }}%"></i></div>
        <div class="stats"><span><small>{{ __('ui.common.players') }}</small><b>{{ $server->current_players }}/{{ $server->max_players }}</b></span><span><small>{{ __('ui.common.ping') }}</small><b>{{ $server->last_ping_ms ?? '—' }} ms</b></span><a href="{{ route('servers.show', $server) }}">{{ __('ui.common.details') }} →</a></div>
    </div>
</article>
