@extends('layouts.admin')
@section('title', __('ui.support.navigation'))
@section('content')
@php
    $repositoryUrl = 'https://github.com/iwebbg/counter-strike-web-laravel';
    $issueUrl = $repositoryUrl.'/issues/new';
    $supportEmail = 'office@interweb.bg';
    $diagnosticsCommand = './docker/compose ps && ./docker/compose logs --tail=200 app scheduler queue nginx database redis';
@endphp
<section class="admin shell support-page">
    @include('admin.partials.navigation')

    <header class="support-heading">
        <span class="eyebrow">{{ __('ui.support.eyebrow') }}</span>
        <h1>{{ __('ui.support.title') }}</h1>
        <p>{{ __('ui.support.description') }}</p>
    </header>

    <div class="support-channel-grid">
        <article class="panel support-card">
            <span class="support-card-icon" aria-hidden="true">GH</span>
            <h2>{{ __('ui.support.report_problem') }}</h2>
            <p>{{ __('ui.support.report_problem_description') }}</p>
            <div class="support-actions">
                <a class="button" href="{{ $issueUrl }}" target="_blank" rel="noopener noreferrer">{{ __('ui.support.open_issue') }} ↗</a>
                <a class="button ghost" href="{{ $repositoryUrl }}" target="_blank" rel="noopener noreferrer">{{ __('ui.support.view_repository') }} ↗</a>
            </div>
        </article>

        <article class="panel support-card">
            <span class="support-card-icon" aria-hidden="true">@</span>
            <h2>{{ __('ui.support.email_support') }}</h2>
            <p>{{ __('ui.support.email_support_description') }}</p>
            <div class="support-actions">
                <a class="button" href="mailto:{{ $supportEmail }}?subject={{ rawurlencode('FragZone support') }}">{{ __('ui.support.send_email') }} →</a>
                <a class="button ghost" href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>
            </div>
        </article>
    </div>

    <section class="panel diagnostics-card" id="steam-id">
        <header>
            <div><span class="eyebrow">{{ __('ui.support.steam_eyebrow') }}</span><h2>{{ __('ui.support.steam_title') }}</h2></div>
            <span class="support-card-icon" aria-hidden="true">&gt;_</span>
        </header>
        <p>{{ __('ui.support.steam_description') }}</p>
        <ol class="support-steps">
            @foreach(__('ui.support.steam_steps') as $step)<li>{{ $step }}</li>@endforeach
        </ol>
        <div class="command-copy-row">
            <pre class="diagnostics-command">status</pre>
            <button class="button ghost" type="button" data-copy="status">{{ __('ui.support.copy_status') }}</button>
        </div>
        <p class="donation-note">{{ __('ui.support.steam_format') }} <code>STEAM_0:1:123456</code>. {{ __('ui.support.steam_warning') }}</p>
    </section>

    <section class="panel diagnostics-card">
        <header>
            <div><span class="eyebrow">{{ __('ui.support.before_reporting') }}</span><h2>{{ __('ui.support.diagnostics_title') }}</h2></div>
            <button class="button ghost" type="button" data-copy="{{ $diagnosticsCommand }}">{{ __('ui.support.copy_diagnostics') }}</button>
        </header>
        <p>{{ __('ui.support.diagnostics_description') }}</p>
        <pre class="diagnostics-command">{{ $diagnosticsCommand }}</pre>
    </section>

    <section class="panel donation-section">
        <header>
            <div><span class="eyebrow">{{ __('ui.support.development') }}</span><h2>{{ __('ui.support.donate_title') }}</h2></div>
            <span class="support-card-icon" aria-hidden="true">♥</span>
        </header>
        <p>{{ __('ui.support.donate_description') }}</p>
        <div class="donation-grid">
            <a class="donation-option" href="https://ko-fi.com/interwebltd" target="_blank" rel="noopener noreferrer"><span class="donation-icon">☕</span><span><strong>Ko-fi</strong><small>{{ __('ui.support.coffee') }}</small></span><b>↗</b></a>
            <a class="donation-option" href="https://buymeacoffee.com/interweb.bg" target="_blank" rel="noopener noreferrer"><span class="donation-icon">☕</span><span><strong>Buy Me a Coffee</strong><small>{{ __('ui.support.one_time') }}</small></span><b>↗</b></a>
            <a class="donation-option" href="https://paypal.me/interwebltd" target="_blank" rel="noopener noreferrer"><span class="donation-icon">P</span><span><strong>PayPal</strong><small>{{ __('ui.support.paypal') }}</small></span><b>↗</b></a>
        </div>
        <p class="donation-note">{{ __('ui.support.payment_notice') }}</p>
    </section>
</section>
@endsection
