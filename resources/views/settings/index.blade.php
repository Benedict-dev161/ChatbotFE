@extends('layouts.app', [
    'title' => 'Settings - Disty Teknologi',
    'pageLabel' => 'Settings'
])

@section('content')
    <form method="POST" action="{{ route('settings.theme') }}" class="settings-form">
        @csrf

        <label class="toggle-row">
            <input type="hidden" name="dark_mode" value="0">

            <input
                type="checkbox"
                name="dark_mode"
                value="1"
                onchange="this.form.submit()"
                {{ session('dark_mode') ? 'checked' : '' }}
            >

            <span class="toggle-slider"></span>
            <span>Dark Mode</span>
        </label>
    </form>
@endsection
