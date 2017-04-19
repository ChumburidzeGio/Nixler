@extends('layouts.app')

@section('content')
<div class="container" ng-controller="ThreadCtrl as vm">

	<script>window.thread = <?php echo json_encode($thread); ?></script>

	<div class="col-md-8">

		<div class="_z013 _bgw _mb2 _clear">

			<div class="_tbs _ov _fs16 _p10 _pl15 _ml5" ng-bind="vm.thread.title"></div>
			<hr class="_m0">

			<div class="_clear _oa _mih150" scroll-glue ng-height>

				<div class="_tbs _tac _bg5 _crp" ng-click="vm.load('-1')" ng-if="vm.isMore()">
				    <span class="_tb">
				        Previous messages
				    </span>
				</div>

				<div class="_clear _media _m15" ng-repeat="msg in vm.thread.messages | orderBy:'id'" >
					<img ng-src="@{{ msg.photo }}" class="_mr10 _left _brds50" ng-if="!msg.own">
					<div class="_clear">
						<div class="_p3 _pr10 _dib _pl10 _brds15 _mt3" 
							ng-class="{'_right _mr10 _bg4 _cw':msg.own,'_tar _bgwd1':!msg.own}" ng-bind="msg.body">
						</div>
					</div>
				</div>
			</div>

			<form ng-submit="vm.message()" class="_bt1 _pt5 _pb5 _posr _bgw _bcg">
				<div class="_li _p5">
					<textarea class="_fe _fs14 _bgw" msd-elastic placeholder="New messages text" ng-model="vm.text" rows="3" autofocus="" ng-keyup="$event.keyCode == 13 && vm.message()"></textarea>
				</div>

				<div class="_li _p10 _clear _pt0 _pb5 _a4">
					<button class="_btn _bg5 _c2 _right">Send</button>
				</div>

			</form>

	</div>

</div>

<div class="col-md-4">


	<div class="_z013 _bgw _clear">

		<a class="_lim _hvrl _tal _bb1" href="{{ route('threads') }}">
						<i class="material-icons _mr15 _va4 _fs18">filter_list</i> All messages
		</a>

		<a class="_lim _hvrl">
			<i class="material-icons _mr10 _va4 _fs18">people</i> Participants
		</a>

		<a class="_lim _hvrl" ng-repeat="i in vm.thread.participants" ng-href="@{{ i.url }}">

			<div class="_media _clear">
				<img ng-src="@{{ i.avatar }}" class="_left _mr10 _brds50" height="22px">
				<span class="_cg" ng-bind="i.name"></span> <small ng-if="i.me"> (You)</small>
			</div>

		</a>
	</div>

</div>
</div>
@stop
