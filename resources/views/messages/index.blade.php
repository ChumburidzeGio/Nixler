@extends('layouts.app')

@section('body_class', 'im-page')

@section('content')
<div class="container" ng-controller="ThreadsCtrl as vm">

	<script>window.threads = <?php echo $threads->toJson(); ?></script>
	<script>window.thread = <?php echo $thread ? json_encode($thread) : ''; ?></script>


	<div class="_bs012 _bgw _brds1">
		<div class="row">
		<div class="col-md-4 _pr0{{ $thread ? ' hidden-sm hidden-xs' : ''  }}" id="threads">

				<div class="_tbs _ov _fs14 _p10 _pl15 _bb1 _bcg _cbl header">
					<a href="{{ url('/') }}">
						<i class="material-icons hidden-lg hidden-md _mr10 _cg _va5 _fs20 _ml5">arrow_back</i>
					</a>
					<i class="material-icons _fs24 _mr15 _va9 _ml5">message</i> @lang('Messages')
				</div>
				@if(count($threads))
				<div class="_os">
					<a class="_media _clear _p10 _pl15 _hvrl _bt1" 
					ng-repeat="thread in vm.threads" 
					ng-href="@{{ thread.link }}"
					ng-class="{'_bgwt8':(vm.thread.id == thread.id)}">
					<img ng-src="@{{ thread.photo }}" class="_mr15 _left _brds3" height="40px" width="40px">
					<div class="_clear _pr10">
						<span class="_c2" ng-bind="thread.name" 
							ng-class="{'_cb _fw600':thread.unread && !(vm.thread.id == thread.id),'_cg':!thread.unread}"></span>
						<small class="_sub _clear _telipsis" 
							ng-class="{'_cb _fw600':thread.unread && !(vm.thread.id == thread.id),'_cg':!thread.unread}">
							<span ng-if="thread.last_replied" class="_c3">@lang('You'):</span>
							<span ng-bind="thread.message"></span>
						</small>
					</div>
				</a>
				</div>
				@else
				<div class="_posr _clear _mih250 _tac _os">
					<div class="_a0 _posa">
						<span class="_fs16 _c2">@lang('A list of dialogs is empty.')</span><br>
						@lang('To contact someone go to profile of this person and click "Message"')
					</div>
				</div>
				@endif

			</div>

			<div class="col-md-8 _pl0" id="thread-messages">

				<div class="_mah85vh _os ng-cloak" ng-if="vm.showThreads" id="thread-messages-threads">
					<div class="_tbs _ov _fs16 _p10 _pl15 _bb1">
						<a href="{{ url('/') }}">
							<i class="material-icons hidden-lg hidden-md _mr10 _cg _va5 _fs20 _ml5">arrow_back</i>
						</a>
						@lang('Messages')
					</div>
					<a class="_media _clear _p10 _pl15 _hvrl _bt1" 
						ng-repeat="thread in vm.threads" 
						ng-href="@{{ thread.link }}" 
						ng-class="{'_bgwt8':(vm.thread.id == thread.id)}">
						<img ng-src="@{{ thread.photo }}" class="_mr15 _left _brds3" height="40px" width="40px">
						<div class="_clear _pr10">
							<span class="_c2" ng-bind="thread.name" 
								ng-class="{'_cb _fw600':thread.unread && !(vm.thread.id == thread.id),'_cg':!thread.unread}"></span>
							<small class="_sub _clear _telipsis" 
								ng-class="{'_cb _fw600':thread.unread && !(vm.thread.id == thread.id),'_cg':!thread.unread}">
								<span ng-if="thread.last_replied" class="_c3">@lang('You'):</span>
								<span ng-bind="thread.message"></span>
							</small>
						</div>
					</a>
				</div>

				<div ng-if="!vm.showThreads" class="_clear _posr" id="thread-messages-content">

				@if($thread)
				<div class="_tbs _ov _fs16 _p10 _pl15 _c3 _bb1 _bcg">
					<i class="material-icons hidden-lg hidden-md _mr10 _cg _va5 _fs20 _ml5" ng-click="vm.showThreads=!vm.showThreads">arrow_back</i>
					<span ng-bind="vm.thread.title" class="_ml5"></span> 
				</div>

				<div class="_clear _oa _mih150 _bt1 _os" scroll-glue>

					<div class="_tbs _tac _bg5 _crp" ng-click="vm.load('-1')" ng-if="vm.isMore()">
						<span class="_tb">
							@lang('Previous messages')
						</span>
					</div>

					<div class="_p10" id="msg-body">
						<div class="_media _clear _p3 _mt10 msg-item _ml10" ng-repeat="msg in vm.thread.messages | orderBy:'id'">
							<a ng-href="@{{ msg.link }}">
								<img class="_mr10 _left _brds3" ng-src="@{{ msg.photo }}" height="38px" width="38px">
							</a>
							<div class="_clear">
								<a class="_title _c4" ng-href="@{{ msg.link }}">
									<span ng-bind="msg.author"></span>
									<small class="_c2 _ml5" ng-bind="msg.time | timeAgo"></small>
								</a>
								<p class="_c3 _anc text _mr15" ng-bind-html="msg.body | to_trusted"></p>
							</div>
						</div>
					</div>

				</div>

				<div id="form-placeholder" class="_clear"></div>
				<form ng-submit="vm.message()" class="_bt1 _pt5 _pb5 _posa _ab _bgwt8 _bcg">

					<div class="_li _p15 _pt5 _pb5 _mr15">
						<div class="_mr15 _pr10 _ml5">
							<textarea class="_fe _fs14 _bgw _b1 _brds3 _pt10 _mr15 _clear" msd-elastic placeholder="@lang('New messages text')" ng-model="vm.text" rows="3" autofocus="" ng-enter="vm.message()"></textarea>
						</div>
					</div>

					<button class="_btn _bg0 _c3 _dib _a3 _posa _mt5 _pr15" ng-class="{'_cbl':vm.inputHasMessage()}">
						<i class="material-icons">send</i>
					</button>

				</form>

				@else 

					<div class="_posr _clear _mih250 _tac _h80vh hidden-sm hidden-xs">
						<div class="_a0 _posa">
							<i class="material-icons _fs40 _clear _tac _cbl _mb15">message</i>
							<span class="_fs16 _cg">@lang('Please choose the dialog to see the conversation.')</span><br>
						</div>
					</div>

				@endif
				</div>

			</div>
		</div>
	</div>
</div>
@stop
