<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'KiddieCheck') }}</title>

    </head>
    
   <body>
        @foreach ($data as $bit)
            <p>{{ $bit->id }} | {{ $bit->name }} | {{ $bit->email }} | {{ $bit->password }} | 
                {{ $bit->role }} | {{ $bit->home_address }} | {{ $bit->status }} | 
                 {{ $bit->profile_path }} | 
            </p>
        @endforeach
    </body>
</html>
