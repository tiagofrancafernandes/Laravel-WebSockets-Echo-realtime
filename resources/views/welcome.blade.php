@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <h2 class="text-center">Users</h2>
    </div>

    <div class="col-12">
        <ul class="list-group">
            @foreach ($users as $user)
                <li class="mb-3 list-group-item">
                    <a href="{{ route('static_login_as', $user->id) }}" class="btn btn-sm btn-outline-info">Login as {{ $user->name }}</a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
