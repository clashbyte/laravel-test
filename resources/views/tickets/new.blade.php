@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form method="POST" action="/home/new" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-header">Новая заявка</div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label for="themeField" class="col-sm-2 col-form-label">Заголовок</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="themeField" name="subject" placeholder="Тема обращения" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="contentField" class="col-sm-2 col-form-label">Текст</label>
                            <div class="col-sm-10">
                                <textarea name="content" id="contentField" placeholder="Отправляемый текст обращения" class="form-control" required></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="attachField" class="col-sm-2 col-form-label">Вложение</label>
                            <div class="col-sm-10">
                                <input type="file" name="attachment" id="attachField">
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <input type="submit" value="Опубликовать" class="btn btn-primary">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
