@extends('layouts.app')
@section('title', __('ui.auth.title'))
@section('content')
<section class="login"><form class="form panel" method="post" action="{{ route('admin.login.store') }}">@csrf<span class="eyebrow">{{ __('ui.auth.eyebrow') }}</span><h1>{{ __('ui.auth.heading') }}</h1><label>Email<input type="email" name="email" value="{{ old('email') }}" required></label><label>{{ __('ui.auth.password') }}<input type="password" name="password" required></label>@error('email')<p class="error">{{ $message }}</p>@enderror<button class="button">{{ __('ui.auth.login') }}</button></form></section>
@endsection
