@extends('layouts.app')

@section('content')
<div class="container" ng-controller="ThreadsCtrl as vm">

<script>window.threads = <?php echo $threads->toJson(); ?></script>

	<div class="col-md-10 col-md-offset-1 _p0">


				<div class="_z013 _bgw _mb10">

					<div class="_tbs _ov _fs16 _p10 _pl15">@lang('Messages')</div>
						@if(count($threads))
						<a class="_media _clear _p10 _hvrl" ng-repeat="thread in vm.threads" ng-href="@{{ thread.link }}" ng-class="{'_bg5':thread.unread}">
								<img ng-src="@{{ thread.photo }}" class="_mr15 _left _brds3">
								<div class="_clear _pr10">
									<span class="_c2" ng-bind="thread.name"></span>
									<small class="_sub _cg _clear">
										<spaan ng-if="thread.last_replied" class="_c4">@lang('You'):</spaan>
										<spaan ng-bind="thread.message"></spaan>
									</small>
								</div>
						</a>
						@else
						<div class="_posr _clear _mih250 _tac">
							<div class="_a0 _posa">
								<span class="_fs16 _c2">@lang('A list of dialogs is empty.')</span><br>
								@lang('To contact someone go to profile of this person and click "Message"')
							</div>
						</div>
						@endif
				</div>
			</div>

			<!--div class="col-md-4">

				<div class="_z013 _bgw _clear">

					<a class="_lim _hvrl _c4 _tal" ui-sref="app.messages">
						<i class="material-icons _mr15 _va4 _fs18">filter_list</i> All messages
					</a>

					<a class="_lim _hvrl _c3 _tal" ui-sref="app.messages">
						<i class="material-icons _mr15 _va4 _fs18">add</i> Start a conversation
					</a>

        </div>

    </div-->
</div>
@stop
