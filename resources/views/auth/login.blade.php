<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/login.css">
    <title>Document</title>
</head>
<body>
    <section class="form-02-main">
      <form class="container" method="POST" action="{{ route('login') }}">
        @csrf
        <div class="row">
          <div class="col-md-12">
            <div class="_lk_de">
              <div class="form-03-main">
                <div class="logo">
                  <img src="images/login/user.png">
                </div>
                <div class="form-group">
                  <input type="email" name="email" class="form-control _ge_de_ol {{ $errors->has('email') ? ' is-invalid': '' }}" placeholder="Enter Email" required aria-required="true" value="{{ old('email') }}">
                  @if ($errors->has('email'))
                    <span class="invalid-feedback">
                      <strong>{{ $errors->first('email') }}</strong>
                    </span> 
                  @endif
                </div>

                <div class="form-group">
                  <input type="password" name="password" class="form-control _ge_de_ol {{ $errors->has('password') ? ' is-invalid': '' }}" placeholder="Enter Password" required aria-required="true">
                  @if ($errors->has('password'))
                    <span class="invalid-feedback">
                      <strong>{{ $errors->first('password') }}</strong>
                    </span> 
                  @endif
                </div>

                <div class="checkbox form-group">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" value="{{ old('remember') ? 'checked': '' }}">
                    <label class="form-check-label" for="remember">
                      Remember me
                    </label>
                  </div>
                  <a href="#">Forgot Password</a>
                </div>

                <div class="form-group text-center d-grid gap-2 col-6 mx-auto">
                    <button type="submit" class="btn btn-primary btn-block">Login!</button>
                </div>
              </div>
            </div>
          </div>
        </div>
    </form>
    </section>
    
</body>
</html>