@extends('layouts.app')

@section('content')
<div class="container">
    <div class="mb-4">
        <h2 class="float-left">Список обращений</h2>
        @if(!$is_manager)
        <a href="/home/new" class="float-right btn btn-primary">Создать</a>
        @endif
        <div class="clearfix"></div>
    </div>


    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif


    @if(count($tickets))
    <div class="card-columns">
        @foreach($tickets as $ticket)
            <div class="card">
                <div class="card-header"><b>№{{ $ticket->id }}</b>. {{ $ticket->subject }}</div>
                <div class="card-body">
                    @if($is_manager)
                        <div class="text-muted small mb-1">
                            {{ $users[$ticket->user]->name }} ({{ $users[$ticket->user]->email }}) пишет:
                        </div>
                    @endif
                    <div>
                        {{ $ticket->content }}
                    </div>
                    @if($ticket->attachment)
                    <a href="/home/file/{{ $ticket->id }}" target="_blank" class="block btn btn-block btn-outline-secondary btn-sm mt-3">Скачать вложение</a>
                    @endif

                    @if($ticket->manager)
                    <div class="card bg-secondary mt-4 text-white">
                        <div class="card-header" style="padding: 0.5rem 0.75rem">Отвечает <b>{{ $users[$ticket->manager]->name }}</b>:</div>
                        <div class="card-body" style="padding: 0.75rem">
                            <div>
                                {{ $ticket->reply }}
                            </div>
                            <div class="small mt-1">Отвечено {{ $ticket->replied_at->format('d.m.Y') }} в {{ $ticket->replied_at->format('H:i') }}</div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="card-footer text-right">
                    <span class="small text-secondary">Создано {{ $ticket->created_at->format('d.m.Y') }} в {{ $ticket->created_at->format('H:i') }}</span>
                    @if($is_manager && !$ticket->manager)
                    &nbsp;
                    <a href="/home/reply/{{ $ticket->id }}" class="btn btn-sm btn-primary">Ответить</a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    @else
        <div class="text-center text-muted">
            Нет обращений.
            @if(!$is_manager)
                <a href="/home/new">Создать новое.</a>
            @endif
        </div>
    @endif

</div>
@endsection
