@extends('layouts.app')

@section('content')
<div class="container" ng-controller="ThreadCtrl as vm">

	<script>window.thread = <?php echo json_encode($thread); ?></script>

	<div class="col-md-8">

		<div class="_z013 _bgw _mb15 _clear _brds3">

			<div class="_tbs _ov _fs16 _p10 _pl15 _ml5" ng-bind="vm.thread.title"></div>
			<hr class="_m0">

			<div class="_clear _oa _mih150" scroll-glue ng-height>

				<div class="_tbs _tac _bg5 _crp" ng-click="vm.load('-1')" ng-if="vm.isMore()">
				    <span class="_tb">
				        @lang('Previous messages')
				    </span>
				</div>

				<div class="_p10">
				<div class="_media _clear _p3 _mt10" ng-repeat="msg in vm.thread.messages | orderBy:'id'">
				    <a ng-href="@{{ msg.link }}">
				    	<img class="_mr10 _left _brds3" ng-src="@{{ msg.photo }}" height="38px" width="38px">
				    </a>
				    <div class="_clear">
				        <a class="_title _c4" ng-href="@{{ msg.link }}">
				            <span ng-bind="msg.author"></span>
				            <small class="_c2 _ml5" ng-bind="msg.time | timeAgo"></small>
				        </a>
				        <p class="_c3 _anc" ng-bind-html="msg.body | to_trusted"></p>
				    </div>
				</div>
				</div>

			</div>

			<form ng-submit="vm.message()" class="_bt1 _pt5 _pb5 _posr _bgw _bcg">
				<div class="_li _p5">
					<textarea class="_fe _fs14 _bgw" msd-elastic placeholder="New messages text" ng-model="vm.text" rows="3" autofocus="" ng-keyup="$event.keyCode == 13 && vm.message()"></textarea>
				</div>

				<div class="_li _p10 _clear _pt0 _pb5 _a4">
					<button class="_btn _bg5 _c2 _right">@lang('Send')</button>
				</div>

			</form>

	</div>

</div>

<div class="col-md-4">


	<div class="_z013 _bgw _clear _brds3">

		<a class="_lim _hvrl _tal _bb1" href="{{ route('threads') }}">
						<i class="material-icons _mr15 _va4 _fs18">message</i> @lang('All messages')
		</a>

		<a class="_lim _hvrl">
			<i class="material-icons _mr10 _va4 _fs18">people</i> @lang('Participants')
		</a>

		<a class="_lim _hvrl" ng-repeat="i in vm.thread.participants" ng-href="@{{ i.url }}">

			<div class="_media _clear">
				<img ng-src="@{{ i.avatar }}" class="_left _mr10 _brds3" height="22px">
				<span class="_cg" ng-bind="i.name"></span> <small ng-if="i.me"> (@lang('You'))</small>
			</div>

		</a>
	</div>

</div>
</div>
@stop
