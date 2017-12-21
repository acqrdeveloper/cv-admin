@extends('layouts.master-login')

@section('content')
    <div class="container-fluid">
        <div class="col-md-6 col-md-offset-3">
            <div class="panel panel-login">
                @if(Session::has('message'))
                        <p class="text-center alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                @endif
                <div class="panel-body text-center">
                    <img src="{{ asset('images/logo-centros.png') }}" alt="">
                </div>
                <div class="panel-body">
                    <form role="form" method="post" action="{{ route('post.login') }}">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <fieldset>
                            <div class="form-group">
                                <label class="control-label">Email</label>
                                <input name="email" type="text" class="form-control" required title="email"
                                       value="{{ old("email") }}">
                                <div class="help-block with-errors">
                                    @if ($errors->has('email'))
                                        <span class="help-block text-primary"><p>{{ $errors->first('email') }}</p></span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Password</label>
                                <input name="password" type="password" class="form-control" required title="password"
                                       value="{{ old("password") }}">
                                <div class="help-block with-errors">
                                    @if ($errors->has('password'))
                                        <span class="help-block"><p>{{ $errors->first('password') }}</p></span>
                                    @endif
                                </div>
                            </div>
                            <div class="checkbox row text-center">
                                <div class="col-sm-5 text-left">
                                    <label>
                                        <input type="checkbox" value="SI">Recordarme
                                    </label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="m-r-5 btn btn-primary btn-block pull-left"><i
                                                class="fa fa-sign-in"></i> Iniciar Sesion
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <a href="{{route('google.login')}}" class="btn btn-danger btn-block pull-right"><i
                                                class="fa fa-google"></i> Iniciar Sesion con Google</a>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
