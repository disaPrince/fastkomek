@extends('layouts.app')
@section('content')
<div id="content-wrapper">
    <div class="container-fluid">
        <!-- DataTables Example -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fas fa-table"></i>
                    Клиенты
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table table-striped table-hover" id="datatable" width="100%" cellspacing="0">
                        <thead class='table-dark'>
                            <tr>
                                <th>Имя</th>
                                <th>Телефон</th>
                                <th>Телеграм</th>
                                <th>TelegramUsername</th>
                            </tr>
                        </thead>
                        <tfoot class='table-dark' >
                            <tr>
                                <th>Имя</th>
                                <th>Телефон</th>
                                <th>Телеграм</th>
                                <th>TelegramUsername</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach($clients as $client)
                            <tr>
                                <td>{{$client->name}}</td>
                                <td>{{$client->phone}}</td>
                                <td>{{$client->telegramId}}</td>
                                 <td>{{$client->telegramUsername}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>
    </div>
</div>

<script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>

<script>
    $(document).ready(function() {
        $('#datatable').DataTable();
    });
</script>
@endsection