<div id="help-modal" ng-controller="HelpModal as vm">

    <div class="_bgcrm _bt1 _br1 _bl1 _a4 _posf _cb _bcg _brds3 _mr15 ng-cloak">
        <div class="_cb _pl15 _pr15 _bcg _fs13 _crp" ng-click="vm.toggle()" ng-class="{'_p10': vm.isOpen(), '_p5': !vm.isOpen()}">
            @lang('Help')
            <i class="material-icons _fs20 _right" ng-if="vm.isOpen()">close</i>
        </div>
        <iframe ng-src="https://www.facebook.com/plugins/page.php?href=https%3A%2F%2Fwww.facebook.com%2Fnixler.georgia%2F&tabs=%20messages&width=340&height=500&small_header=false&adapt_container_width=true&hide_cover=false&show_facepile=true&appId=1610355969221723" width="340" height="500" class="_oh _b0" scrolling="no" frameborder="0" allowTransparency="true" ng-if="vm.isOpen()"></iframe>
    </div>
</div>