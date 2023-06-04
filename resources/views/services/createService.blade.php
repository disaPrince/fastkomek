@extends('layouts.app')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
@section('content')
    <div id="content-wrapper">
        <div class="container-fluid">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fas fa-table"></i>
                </div>
                <div class="card-body">
                    <form id="bookingRoom" action=""
                          method="post" enctype="multipart/form-data" >
                        {{csrf_field()}}
                        <div class="form-group">
                            <label>Название услуги на русском языке</label>
                            <div class="input-group">
                                <input class="form-control mt-1" name="ruName" required type="text">
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <label>Название услуги на казахском языке</label>
                            <div class="input-group">
                                <input class="form-control mt-1" name="kzName" required type="text">
                            </div>
                        </div>
                        <div class="input-group mt-3">
                            <button type="submit" class="btn btn-primary">Сохранить</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
