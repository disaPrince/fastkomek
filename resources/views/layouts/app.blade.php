<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex,nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>K-bot</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css"
          integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <!-- Bootstrap core CSS-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
    
</head>
<body id="page-top">
<nav class="navbar navbar-expand navbar-dark static-top fixed-top" style="background: #1d1b31">
    <a class="navbar-brand mr-1 mx-3" href="">FastKomek</a>
        <i class="fas fa-bars" style="cursor: pointer"></i>
     <div style="margin-left: 85%">
            <a class="me-2 d-flex text-white" href="{{ route('logout') }}"
                    onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                    >Logout</a>

                <form id="logout-form" action={{ route('logout') }} method="POST"
                    style="display: none;">
                    @csrf
                </form>            
    </div>
</nav>
<div class="sidebar">
    <ul class="nav-links">
        <li>
            <div class="icon-link">
                <a href="{{ route('index') }}">
                    <i class="fa fa-paper-plane"></i>
                    <span class="link_name">Рассылка с реакциями</span>
                </a>
            </div>
            <ul class="sub-menu">
                <li>
                    <a class="link_name" href="{{ route('index') }}">
                        {{-- <i class="fa fa-paper-plane"></i> --}}
                        <i>Рассылка с реакциями</i>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <div class="icon-link">
                <a href="{{ route('export.reactions.view') }}">
                    <i class="fa fa-file-export"></i>
                    <span class="link_name">Выгрузка «Рассылки с реакциями»</span>
                </a>
            </div>
            <ul class="sub-menu">
                <li>
                    <a class="link_name" href="{{ route('export.reactions.view') }}">
                        {{-- <i class="fa fa-file-export"></i> --}}
                        <i>Выгрузка «Рассылки с реакциями»</i>
                    </a>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <div class="icon-link">
                <a href="{{ route('showStaff') }}">
                    <i class="fa fa-users"></i>
                    <span class="link_name">Клиенты</span>
                </a>
            </div>
            <ul class="sub-menu">
                <li>
                    <a class="link_name" href="{{ route('showStaff') }}">
                        <i>Клиенты</i>
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <div class="icon-link">
                <a href="{{ route('service.show') }}">
                    <i class="fas fa-clipboard"></i>
                    <span class="link_name">Спектр услуг</span>
                </a>
            </div>
            <ul class="sub-menu">
                <li>
                    <a class="link_name" href="{{ route('service.show') }}">
                        <i>Спектр услуг</i>
                    </a>
                </li>
            </ul>
        </li>
    </ul>
</div>

<div class="container-fluid home-section">
    @yield('content')
</div>
<!-- Bootstrap core JavaScript-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" 
integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="{{asset('js/jquery.min.js')}}"></script>
<script>
    let arrow = document.querySelectorAll('.arrow');
    for (let index = 0; index < arrow.length; index++) {
        arrow[index].addEventListener('click', (e) => {
        let arrowParent = e.target.parentElement.parentElement;
        arrowParent.classList.toggle('showMenu');
        });
    }

    let sidebar = document.querySelector('.sidebar');
    let sidebarBtn = document.querySelector('.fa-bars');
    sidebarBtn.addEventListener('click', () => {
        sidebar.classList.toggle('close-side');
    });

    let subArrow = document.querySelectorAll('.arrow-two');
    for (let index = 0; index < subArrow.length; index++) {
        subArrow[index].addEventListener('click', (e) => {
        let subArrowParent = e.target.parentElement.parentElement;
        subArrowParent.classList.toggle('showSubMenu');
        });
    }

    let subArrowThree = document.querySelectorAll('.arrow-three');
    for (let index = 0; index < subArrowThree.length; index++) {
        subArrowThree[index].addEventListener('click', (e) => {
        let subArrowThreeParent = e.target.parentElement.parentElement;
        subArrowThreeParent.classList.toggle('showSubMenuTwo');
        });
    }
    

    let subArrowFour = document.querySelectorAll('.arrow-four');
    for (let index = 0; index < subArrowFour.length; index++) {
        subArrowFour[index].addEventListener('click', (e) => {
        let subArrowFourParent = e.target.parentElement.parentElement;
        subArrowFourParent.classList.toggle('showSubMenuThree');
        });
    }
</script>
</body>
</html>
