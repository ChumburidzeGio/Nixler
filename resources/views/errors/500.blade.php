@extends('layouts.general')

@section('body_class', '_bga')

@section('app')

<div class="container _tac _cb">
<br>
<br>

    <h1 class="_fw300 _fs40">Something went wrong...</h1>
    <h2 class="_fw300 _mb15 _fs28">The server encountered an error and could not complere your request.</h2>

    <div class="_ab _tac _mb15">
	    <span class="_m15">Error</span>
	    <span class="_m15">შეცდომა</span>
	    <span class="_m15">Błąd</span>
	    <span class="_m15">Ошибка</span>
	    <span class="_m15">Помилка</span>
	    <span class="_m15">Fehler</span>
	    <span class="_m15">خطأ</span>
	    <span class="_m15">エラー</span>
	    <span class="_m15">Hata</span>
	    <span class="_m15">錯誤</span>
    </div>

	<br><br>
    <a href="javascript:history.back()" class="_btn _bgw _bs012 _fs14 _pt5 _cbl _mt15 _pb5">
    	<i class="material-icons _fs20 _va5 _mr10">arrow_back</i>
    	Go Back
    </a>
    
</div>

@endsection
