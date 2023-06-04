@extends('layouts.app')
@section('content')
    <div id="content-wrapper">
        <div class="container-fluid">
            <!-- DataTables Example -->
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="fas fa-table"></i>
                        Рассылки
                    </div>
                    <div class="card-body">
                        {{-- <form action="{{route('export.get.reactions')}}" method="post"> --}}
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label for="example-date-input" class="offset-1 col-form-label">Период с :</label>
                                    <div class="col-10">
                                        <input class="form-control" type="date" value="{{date('Y-01-01')}}" name="dateStart" id="dateStart">
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="example-date-input" class="offset-1 col-form-label">Период по :</label>
                                    <div class="col-10">
                                        <input class="form-control" type="date" value="{{date('Y-m-d')}}" name="dateEnd" id="dateEnd">
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-success ml-3 mt-2">Поиск</button>
                        {{-- </form> --}}
                    </div>
                </div>
                <br>
                <div class="table-responsive">
                    <table class="table table-bordered table table-striped table-hover" id="datatable" width="100%" cellspacing="0">
                    <thead class='table-dark'>
                    <tr>
                    <th>Название</th>
                    <th>Дата создания</th>
                    <th></th>
                    </tr>
                    </thead>
                    <tfoot class='table-dark' >
                    <tr>
                    <th>Название</th>
                    <th>Дата создания</th>
                    <th></th>
                    </tr>
                    </tfoot>
                    <tbody>
                    <tr>
                    <td></td>
                    <td></td>
                    <th></th>
                    </tr>
                    </tbody>
                    </table>
                </div>
        </div>
    </div>

<script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>

    <script type="text/javascript">
    
        function select_reaction(id){
                const div = document.createElement('div');
                div.innerHTML = `
                                <html>
                                 <head></head><body><form method='POST' action='{{route('export.reaction')}}'>
                                    @csrf
                                    <input type="hidden" name="id" value=${id}>
                                    </form>
                                    </body>
                                </html>
                                 `;
                $(document.body).append(div);
                $(div).find('form').submit();
        }

        $(document).ready(function(){ /* PREPARE THE SCRIPT */
          $("button").click(function(){ /* WHEN YOU CHANGE AND SELECT FROM THE SELECT FIELD */
            const dateStart = $('#dateStart').val(); /* GET THE VALUE OF THE SELECTED DATA */
            const dateEnd = $('#dateEnd').val();
            const _token   = $('meta[name="csrf-token"]').attr('content');
            
            $('#datatable').DataTable().clear().destroy();

            $.ajax({ /* THEN THE AJAX CALL */
              type: "POST", /* TYPE OF METHOD TO USE TO PASS THE DATA */
              url: "{{route('export.get.reactions')}}",
               /* PAGE WHERE WE WILL PASS THE DATA */
              data: {
                dateStart: dateStart,
                dateEnd: dateEnd,
                _token : _token
              }, /* THE DATA WE WILL BE PASSING */
              success:function(data){
                    console.log(data);
                    $('#datatable').dataTable({
                        data: data,
                        columns: [
                            {
                                'data': function(data){
                                    if(data.title == null){
                                        if(data.content.length > 20){
                                            return `${data.content.slice(0, 20)}...`;
                                        }else{
                                            return `${data.content.slice(0, 20)}`;
                                        }
                                    }else{
                                        return data.title;
                                    }
                                }
                            },
                                {'data': function(data){
                                    // let date = new Date(data.updated_at);
                                    // let allDate = `${date.getFullYear()}-${date.getMonth()}-${date.getDay()}
                                    // ${date.getHours()}:${date.getMinutes()}:${date.getSeconds()}`
                                    // return allDate;
                                    

                                    const t = new Date(data.updated_at);
                                    const date = ('0' + t.getDate()).slice(-2);
                                    const month = ('0' + (t.getMonth() + 1)).slice(-2);
                                    const year = t.getFullYear();
                                    const hours = ('0' + t.getHours()).slice(-2);
                                    const minutes = ('0' + t.getMinutes()).slice(-2);
                                    const seconds = ('0' + t.getSeconds()).slice(-2);
                                    const time = `${date}/${month}/${year}, ${hours}:${minutes}:${seconds}`;
                                    return time;

                                }},
                                {'data': 'id',
                                 'render': function(id){
                                    return `<button onclick="select_reaction(${id})" type="button" class=" container btn btn-success">Выгрузить</button>`
                                 }
                            }
                        ]
                    });
                  }
            });
          });
        });
      </script>

@endsection