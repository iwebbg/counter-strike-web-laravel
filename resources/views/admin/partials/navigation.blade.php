<nav class="admin-nav" aria-label="{{ __('ui.admin.navigation') }}">
    <a href="{{ route('admin.dashboard') }}" @class(['is-active' => request()->routeIs('admin.dashboard')])>{{ __('ui.admin.dashboard') }}</a>
    <a href="{{ route('admin.servers.index') }}" @class(['is-active' => request()->routeIs('admin.servers.*')])>{{ __('ui.admin.servers') }}</a>
    <a href="{{ route('admin.categories.index') }}" @class(['is-active' => request()->routeIs('admin.categories.*')])>{{ __('ui.admin.categories') }}</a>
    <a href="{{ route('admin.news.index') }}" @class(['is-active' => request()->routeIs('admin.news.*')])>{{ __('ui.admin.news') }}</a>
    <a href="{{ route('admin.settings.edit') }}" @class(['is-active' => request()->routeIs('admin.settings.*')])>{{ __('ui.admin.seo_settings') }}</a>
    <form method="post" action="{{ route('admin.logout') }}">@csrf<button type="submit">{{ __('ui.admin.logout') }}</button></form>
</nav>
