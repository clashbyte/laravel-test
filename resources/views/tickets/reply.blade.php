@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><b>№{{ $ticket->id }}</b>. {{ $ticket->subject }}</div>
                <div class="card-body">
                    <div class="text-muted small mb-1">
                        {{ $user->name }} ({{ $user->email }}) пишет:
                    </div>
                    <div>
                        {{ $ticket->content }}
                    </div>
                    @if($ticket->attachment)
                        <a href="/home/file/{{ $ticket->id }}" target="_blank" class="block btn btn-block btn-outline-secondary btn-sm mt-3">Скачать вложение</a>
                    @endif
                </div>
                <div class="card-footer text-right">
                    <span class="small text-secondary">Создано {{ $ticket->created_at->format('d.m.Y') }} в {{ $ticket->created_at->format('H:i') }}</span>
                </div>
            </div>

            <form method="POST" action="/home/reply/{{ $ticket->id }}">
                @csrf
                <div class="card">
                    <div class="card-header">Ответ на заявку</div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="replyField" class="col-sm-2 col-form-label">Ответ</label>
                            <div class="col-sm-10">
                                <textarea name="reply" id="replyField" placeholder="Отправляемый текст ответа" class="form-control" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <input type="submit" value="Отправить ответ" class="btn btn-primary">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
