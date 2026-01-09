@component('components.layout')
    <h1>Liste des utilisateurs</h1>
    <ul>
        @foreach ($users as $user)
            <li><b>{{ $user['first_name'] }} {{ $user['last_name'] }} </b> : {{ $user['email'] }}"</li>
        @endforeach
    </ul>
@endcomponent
