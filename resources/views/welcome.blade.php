<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'KiddieCheck') }}</title>

    </head>
    
   <body>
        <h1>KiddieCheck: Data Sanity Check</h1>

        <section>
            <h2>Users ({{ $users->count() }})</h2>
            <ul>
                @foreach ($users as $u)
                    <li>
                        [#{{ $u->id }}] {{ $u->name }} ({{ $u->email }}) role={{ $u->role }} status={{ $u->status }}
                        @if($u->profile_path) pic={{ $u->profile_path }} @endif
                        @if($u->family) | family id={{ $u->family->id }} @endif
                        @if($u->teacher) | teacher id={{ $u->teacher->id }} @endif
                    </li>
                @endforeach
            </ul>
        </section>

        <section>
            <h2>Families ({{ $families->count() }})</h2>
            <ul>
                @foreach ($families as $f)
                    <li>
                        [#{{ $f->id }}] {{ $f->name }} addr={{ $f->home_address }} user={{ $f->user?->email }}
                        | students: {{ $f->students->pluck('name')->join(', ') }}
                    </li>
                @endforeach
            </ul>
        </section>

        <section>
            <h2>Teachers ({{ $teachers->count() }})</h2>
            <ul>
                @foreach ($teachers as $t)
                    <li>
                        [user #{{ $t->id }}] {{ $t->user?->name }} status={{ $t->status }}
                        | students: {{ $t->students->pluck('name')->join(', ') }}
                    </li>
                @endforeach
            </ul>
        </section>

        <section>
            <h2>Students ({{ $students->count() }})</h2>
            <ul>
                @foreach ($students as $c)
                    <li>
                        [#{{ $c->id }}] {{ $c->name }} ({{ $c->gender }}) status={{ $c->status }}
                        @if($c->profile_path) pic={{ $c->profile_path }} @endif
                        | family={{ $c->family?->name }} | section={{ $c->section?->name }}
                        | teachers: {{ $c->teachers->pluck('user.name')->join(', ') }}
                        | tags: {{ $c->tags->pluck('tag_type')->join(', ') }}
                    </li>
                @endforeach
            </ul>
        </section>

        <section>
            <h2>Domains & Questions ({{ $domains->count() }} domains)</h2>
            <ul>
                @foreach ($domains as $d)
                    <li>
                        {{ $d->name }}: {{ $d->questions->count() }} questions
                    </li>
                @endforeach
            </ul>
        </section>

        <section>
            <h2>Tests ({{ $tests->count() }})</h2>
            <ul>
                @foreach ($tests as $t)
                    <li>
                        [#{{ $t->id }}] student={{ $t->student?->name }} date={{ $t->test_date }} status={{ $t->status }}
                        | observer={{ $t->observer?->role }} {{ $t->observer?->email }}
                        | responses={{ $t->responses->count() }} scores={{ $t->scores->count() }} pictures={{ $t->pictures->count() }}
                    </li>
                @endforeach
            </ul>
        </section>

        <section>
            <h2>Test Responses ({{ $responses->count() }})</h2>
            <ul>
                @foreach ($responses as $r)
                    <li>
                        test={{ $r->test_id }} q={{ $r->question?->question_text }} score={{ $r->score }}
                        @if($r->comment) comment={{ $r->comment }} @endif
                    </li>
                @endforeach
            </ul>
        </section>

        <section>
            <h2>Domain Scores ({{ $scores->count() }})</h2>
            <ul>
                @foreach ($scores as $s)
                    <li>
                        test={{ $s->test_id }} domain={{ $s->domain?->name }} raw={{ $s->raw_score }} scaled={{ $s->scaled_score }}
                    </li>
                @endforeach
            </ul>
        </section>

        <section>
            <h2>Student Tags ({{ $tags->count() }})</h2>
            <ul>
                @foreach ($tags as $tag)
                    <li>
                        student={{ $tag->student?->name }}: {{ $tag->tag_type }} {{ $tag->notes }}
                    </li>
                @endforeach
            </ul>
        </section>

        <section>
            <h2>Test Pictures ({{ $pictures->count() }})</h2>
            <ul>
                @foreach ($pictures as $p)
                    <li>
                        test={{ $p->test_id }} q={{ $p->question?->id ?? 'none' }} file={{ $p->file_path }}
                    </li>
                @endforeach
            </ul>
        </section>

        <p><a href="/register">Add User</a></p>
    </body>
</html>
