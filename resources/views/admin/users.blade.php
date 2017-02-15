@extends('admin.layout')

@section('header')
Users
@endsection

@section('wrapper')
<div class="row _pt15">

  <div class="col-xs-12">


    <div class="_bgw _b1 _brds3 _clear">
      <span class="_pl15 _mt10 _mb5 _cg _fs17 _clear">

        All users

        <div class="_right _mr15">
          <input class="_fe _b1 _brds3 _fes" placeholder="Search in users">
        </div>
      </span>

      <div class="_clear">
        <table class="_w100 _m15">
          <tr class="_bb1">
            <th class="_pb5">Username <i class="material-icons _va7">arrow_drop_up</i></th>
            <th>Full name</th> 
            <th>Email</th>
            <th>Created at</th>
            <th>Last login at</th>
          </tr>
          @for ($i = 0; $i < 10; $i++)
          <tr class="_bt1">
            <td class="_pt10 _pb5">
              <img src="{{ auth()->user()->photo('resize:30x30')}}" height="30" width="30" class="_z013 _brds3 _dib _left _mr10"> chumburidzegio</td>
              <td class="_pt10 _pb5">{{ auth()->user()->name }}</td> 
              <td class="_pt10 _pb5">{{ auth()->user()->email }}</td>
              <td class="_pt10 _pb5">{{ auth()->user()->created_at->format('F jS, Y') }}</td>
              <td class="_pt10 _pb5">{{ auth()->user()->updated_at->format('F jS, Y') }}</td>
            </tr>
          @endfor
          </table>

        </div>
        </div>

    </div>

  </div>
  @endsection