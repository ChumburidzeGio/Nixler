@extends('layouts.app')

@section('body_class', '_bgcrm')

@section('content')

<script>
    window.collection = <?php echo json_encode([
        'items' => $collection->items,
        'isPrivate' => $collection->is_private,
        'privacyOptions' => $privacyOptions
    ]); ?>
</script>

<div class="container _mt50" ng-controller="CollectionUpdateCtrl as vm">

    <div class="row">

        <div class="col-xs-8 col-xs-offset-2">

            <form role="form" class="_crd _mb15" method="POST" action="{{ route('collections.store') }}">

                {{ csrf_field() }}

                <input type="hidden" name="id" value="{{ $collection->id }}">

                <h2 class="_crd-header _bg0">
                    {{ $collection->id ? __('Update collection') : __('Create new collection') }}
                </h2>

                <div class="_crd-content _p15 _bgwt6">

                    <div class="_clear">

                        <div class="_mb15 row">

                            <div class="col-sm-7">

                                <input class="_b1 _fe _brds3 _w100 _fes" type="text" placeholder="@lang('Collection name')" name="name" required 
                                value="{{ $collection->name }}" style="height: 37px;">

                                @if ($errors->has('title'))
                                <span class="_pt1 _pb1 _clear _cr">{{ $errors->first('title') }}</span>
                                @endif

                            </div>

                            <div class="col-sm-5">

                                <select selector model="vm.isPrivate" class="_b1 _bcg _brds3 _bgw" 
                                options="vm.privacyOptions" require="1" value-attr="key"></select>

                                <input type="hidden" name="is_private" ng-value="vm.isPrivate">

                                @if ($errors->has('is_private'))
                                <span class="_pt1 _pb1 _clear _cr">{{ $errors->first('is_private') }}</span>
                                @endif

                            </div>

                        </div>

                        <div>
                            <textarea class="_b1 _fe _brds3 _w100" 
                            msd-elastic placeholder="@lang('Collection description')" 
                            ng-model="vm.text" rows="5" name="description"
                            style="padding-top: 8px;min-height: 50px;" required ng-value="'{{ $collection->description }}'"></textarea>

                            @if ($errors->has('description'))
                            <span class="_pt1 _pb1 _clear _cr">{{ $errors->first('description') }}</span>
                            @endif
                        </div>

                    </div>

                </div>

                <div class="_crd-content _bt1 _bcg ng-cloak">

                    <div class="_posr">
                        <div class="_a8">
                            <i class="material-icons _fs20 _ml15 _mt10" ng-if="!vm.searchProccessing">search</i>
                            <div class="_spnr" style="margin: 40px 28px;" ng-if="vm.searchProccessing"></div>
                        </div>

                        <input class="_fe" type="text" 
                        placeholder="@lang('Find products')" 
                        value="{{ old('name') }}"
                        autocomplete="off" 
                        style="padding-left: 50px;" 
                        ng-model="vm.searchQuery">

                        <i class="material-icons _a3 _fs20 _mr15 _cg _crp" ng-if="vm.searchQuery.length" ng-click="vm.clearSearch()">clear</i>
                    </div>

                    <div class="_bt1 _bcg _posr _pt10 _pb10 ng-cloak" sv-root sv-part="vm.products">

                        <div class="_lim _clear _pl15 _pr15 _hvrl" ng-repeat="item in vm.products" ng-click="vm.remove(item)" sv-element="opts">

                            <img ng-src="@{{ item.photo }}" class="_z013 _brds2 _dib _left" height="30" width="30">

                            <div class="_pl15 _pr15 _oh _pt5">
                                <span class="_cbt8 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs15" ng-bind="item.title"></span>
                            </div>

                            <i class="material-icons _fs20 _a3 _mr10" ng-class="{'_cbl': item.selected}">check_circle</i>

                        </div>

                        <span class="_c2 _lh1 _mb0 _fs15 _p15 _clear _pb10 _cbl" ng-if="vm.results.length">
                            @lang('Global search results')
                        </span>

                        <div class="_lim _clear _pl15 _pr15 _hvrl" ng-repeat="item in vm.results" ng-click="vm.add(item)">

                            <img ng-src="@{{ item.photo }}" class="_z013 _brds2 _dib _left" height="30" width="30">

                            <div class="_pl15 _pr15 _oh _pt5">
                                <span class="_cbt8 _lh1 _mb0 _telipsis _w100 _clear _pr10 _fs15" ng-bind="item.title"></span>
                            </div>

                            <i class="material-icons _fs20 _a3 _mr10">add</i>

                        </div>

                        <div class="_clear _p15 _posr" ng-if="!vm.results.length && !vm.products.length">

                            <div class="_m15 _tac _p15">
                                <div class="_m15 _p15">
                                    <i class="material-icons _fs40 _mb15 _pb0" style="color: #cdd1d7;">shopping_basket</i>
                                    <p style="color: #939393;">No products to show.</p>
                                </div>
                            </div>

                        </div>

                        <input type="hidden" name="items" ng-value="vm.productIds | json">

                    </div>

                </div>


                <div class="_p15 _bt1 _clear _tar _bcg">

                    @if($collection->id)
                    <a class="_btn _bg5 _cg _hvra _left" ng-click="vm.delete()" confirm-click="@lang('Do you really want to delete collection?')">
                        @lang('Delete')
                    </a>
                    @endif

                    <button class="_btn _bga _cb _hvra _ml10" type="submit"> 
                        {{ $collection->id ? __('Update') : __('Publish') }}
                    </button>

                </div>

            </form>

            <form id="delete-form" action="{{ route('collections.delete') }}" method="POST" class="_d0">
                <input type="hidden" name="id" value="{{ $collection->id }}">
                {{ csrf_field() }}
            </form>

        </div>

    </div>

</div>

@endsection