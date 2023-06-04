@extends('layouts.app')
@section('content')
<div id="content-wrapper">
    <div class="container-fluid">
        <!-- DataTables Example -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fas fa-table"></i>
                    Спектр услуг
                </div>
                <div class="card-body">
                    <a href="{{route('service.createView')}}" class="btn btn-primary mx-1 mb-3">+ Добавить</a>
                    <div class="table-responsive">
                        <table class="table table-bordered table table-striped table-hover" id="datatable" width="100%" cellspacing="0">
                        <thead class='table-dark'>
                            <tr>
                                <th>Название на русском</th>
                                <th>Название на казахском</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tfoot class='table-dark' >
                            <tr>
                                <th>Название на русском</th>
                                <th>Название на казахском</th>
                                <th>Действия</th>
                            </tr>
                        </tfoot>
                        <tbody>
                            @foreach($serviceList as $service)
                                <tr>
                                    <td>{{$service->ruName}}</td>
                                    <td>{{$service->kzName}}</td>
                                    <td>
                                        <div class="d-flex flex-row">
                                            <a title="Комнаты" href="{{route('service.topics', [$service->id])}}" class="btn btn-success">
                                                Темы
                                            </a>
                                            <a title="Редактировать" href="{{route('service.edit', ['id' => $service->id])}}" class="mx-1 btn btn-primary btn-xs">
                                                <i class="far fa-edit"></i>
                                            </a>
                                            <a title="Удалить" href="{{route('service.delete', ['id' => $service->id])}}" class="mx-1 btn btn-danger btn-xs">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
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
