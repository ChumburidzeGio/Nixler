<div class="{{ is_integer($key) ? 'col-sm-4' : 'col-sm-6' }}">
        <div class="_lim _clear _pl0 _m15 _bcg _pt15" style="min-height: 200px">
            <i class=" material-icons _mt15 _fs70 _c4">{{ $feature['icon'] }}</i>
            <div class="_pl15 _pr15 _pb10 _oh _mt10">
                @if(!is_integer($key))
                <span class="_lh1 _mb0 _clear _fs13 _ttu">
                    {{ $key }}
                </span>
                @endif
                <span class="_lh1 _mb0 _clear _fs18 _fw600 _ls5">
                    {{ $feature['title'] }}
                </span>
                <span class="_clear _fs17 _oh _cg">
                    {{ $feature['description'] }}
                </span>
            </div>
        </div>
</div>