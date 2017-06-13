<div class="_clear" ng-controller="CommentsCtrl as vm">

<script>window.comments = <?php echo $comments->toJson(); ?></script>
<script>window.comments_target = <?php echo $id; ?></script>

@can('create', \App\Entities\Comment::class)
<form ng-submit="vm.commentPush()" class="_p3 _fg" id="comment-form">
     <div class="_li _p0 _media">
        <img class="_mr10 _left _brds3" src="{{ auth()->user()->avatar('comments') }}" height="40px" width="40px">
        <div class="_clear">
            <textarea class="_fe _b1 _fs13 _brds3" msd-elastic="" placeholder="@lang('Your comment ...')" ng-model="vm.comment_text" rows="3"  ng-click="showbtn=1" ng-keyup="$event.keyCode == 13 && vm.commentPush()"></textarea>
        </div>
    </div>

    <div class="_li _p10 _clear _pr0 _pb5" ng-if="showbtn">
     <button class="_btn _bg4 _cw _right">@lang('Send')</button>
 </div>
</form>
@endcan

@cannot('create', \App\Entities\Comment::class)
<a class="_cb _bg5 _posr _fs14 _brds3 _crp _clear _p5 _tac" href="{{ route('login') }}">
    @lang('Please sign in to write the comment')
</a>
@endcannot

<div id="comments">
    <div class="_media _clear _p3 _mt10 _pb0" ng-repeat="comment in vm.comments">
        <img class="_mr10 _left _brds3" ng-src="@{{ comment.avatar }}">
        <div class="_clear">
            <span class="_title _c4">
                <span ng-bind="comment.author"></span>
                <small class="_c2 _ml5" ng-bind="comment.time | timeAgo"></small>
                <span ng-show="comment.can_delete" confirm-click="@lang('Are you sure?')" class="" ng-click="vm.delete(comment)">
                    <span class="_crp _fs12 _ls5 _c2 _mr10 _dib _p3 _pb0 _pt0 _brds3  _bg5 _right">delete</span>
                </span>
            </span>
            <p class="_c3 _mb5" ng-bind-html="comment.text"></p>
        </div>
    </div>
</div>

<div class="_tbs _tac _bg5 _mt15 _crp" ng-click="vm.load()" ng-if="vm.isMore()">
    <span class="_tb">
        @lang('More comments')
    </span>
</div>
</div>