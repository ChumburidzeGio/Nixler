<div id="products">

		<div class="row _mb15" ng-if="vm.stream.items.length">

			<div class="col-lg-3 col-sm-4 col-xs-6 _cxxs12 _pb15 _mb10 _crp" ng-repeat="product in vm.stream.items">
				<a class="_clear _brds3 _hvrcard _posr" ng-href="@{{ product.url }}">

					<img preload-image ng-src="@{{ product.photo }}" class="_db _w100">

					<div class="_pl10 _pr15 _pt10 _pb10">
						<span class="_cb _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs14" ng-bind="product.title"></span>
						<span class="_cg _clear _fs12  _telipsis _w100 _oh _pr10">
							<b class="_ci" ng-bind="product.price"></b> · @{{ product.owner }} · <i class="material-icons _fs13 _va2 _cg">favorite</i> @{{ product.likes_count }}
						</span>
					</div>

        			<div class="_posa _a8 _p5 _bgr _mt30 _cw" ng-bind="product.discount" ng-if="product.discount"></div>

				</a>
			</div>

		</div>

		<div class="_tbs _tac _bg5 _mt15 _crp _clear" ng-click="vm.load()" ng-if="vm.isMore()">
			<span class="_tb">
				@lang('More products')
			</span>
		</div>

		<div class="_tac _pt15 _mt70 _c3" ng-if="!vm.stream.items.length">
			<h5 class="_fw400">@lang('There is no products to show.')</h5>
		</div>
		
</div>