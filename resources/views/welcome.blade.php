@extends('layouts.app')
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.5/dist/css/select2.min.css" rel="stylesheet" />
@section('content')

    <div id="content-wrapper">

        <div class="container-fluid">
            <!-- DataTables Example -->
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-table"></i>
                        Рассылка с реакциями
                    </div>
                    <div class="card-body">
                        <form id="sendNotifications" action="{{ route('sendNotifications') }}"
                              method="post" enctype="multipart/form-data" >
                            {{csrf_field()}}
                            <div class="form-group">
                                <label for="title">Название</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <br>
                            <div class="form-group">
                                <label for="company">Получатели</label>
                                <select class="form-control" id="company" name="company"  onchange="checkType('m')">
                                    <option value="user">Нескольким пользователям</option>
                                    <option value="" selected>Всем пользователям</option>
                                </select>
                            </div>
                            <div class="form-group" id="usersSelectMailing" style="display: none">
                                <br><label for="company">Получатели</label>
                                <select id="users" class="multiple-1" name="users[]" style="width: 100%;" multiple>
                                    @foreach ($users as $user)
                                            <option value="{{$user->telegramId}}">{{$user->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <br>
                            <div class="form-group">
                                <label>Сообщение</label>
                                <div class="input-group">
                                    <textarea class="form-control" name="message"></textarea>
                                </div>
                            </div>
                            <br>
                            <div class="form-group">
                                <label for="photoUpload">Загрузить картинку</label><br>
                                <input class="form-control-file" id="photoUpload" name="photoUpload[]" type="file" multiple><br>
                                <span style="color:red">Для выбора несколько файлов нажмите кнопку "Ctrl"</span>
                            </div>
                                <div class="input-group mt-2">
                                    <button type="submit" class="btn btn-primary">Отправить</button>
                                </div>
                        </form>
                    </div>
                </div>
        </div>
       
    </div>

    <script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="https://rawgit.com/select2/select2/4.0.5/dist/js/select2.full.min.js"></script>
    <script>
        function typeMessages(elem){
            let type = $('#messageType').val();
            if(type === 'reactions-without-links'){
                $('#link').hide();
            }else if(type === 'message'){
                $('#link').hide();
            }else if(type === 'poll'){
                $('#link').hide();
            }else{
                $('#link').show();
            }
        }
    </script>
    <script>
        $(".multiple-1").select2();
        $(".multiple-2").select2();
        function checkType(elem){
            let type = $('#company').val();
            if(type === 'user'){
                $('#usersSelectMailing').show();
                $('#companiesSelectMailing').hide();
            }else if(type === 'companies'){
                $('#usersSelectMailing').hide();
                $('#companiesSelectMailing').show();
            }else{
                $('#usersSelectMailing').hide();
                $('#companiesSelectMailing').hide();
            }
        }

        function getTypeOfMessages(){
            $('#messageType').val('message').change();
        }

        window.onload = getTypeOfMessages();

    </script>
@endsection



