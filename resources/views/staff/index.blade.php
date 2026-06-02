@extends('layouts.app', [
    'title' => 'Staff - Disty Teknologi',
    'pageLabel' => 'Staff'
])

@section('content')
    <div class="staff-list">
        @forelse ($staff as $person)
            <article class="staff-item">
                <div class="avatar-box">♙</div>

                <div>
                    <h3>{{ $person->full_name }}</h3>

                    <p class="{{ $person->status === 'active' ? 'available' : 'unavailable' }}">
                        {{ $person->status === 'active' ? 'Available' : 'Unavailable' }}
                    </p>
                </div>
            </article>
        @empty
            <p>No staff found.</p>
        @endforelse
    </div>
@endsection