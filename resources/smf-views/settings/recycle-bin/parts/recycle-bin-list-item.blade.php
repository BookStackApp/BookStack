<div class="item-list-row flex-container-row items-center px-s wrap">
    <div class="flex-2 px-m py-xs min-width-xl">
        <div class="flex-container-row items-center py-xs">
            <span role="presentation" class="flex-none icon text-{{$deletion->deletable->getType()}}">@icon($deletion->deletable->getType())</span>
            <div class="text-{{ $deletion->deletable->getType() }}">
                {{ $deletion->deletable->name }}
            </div>
        </div>
        @if($deletion->deletable instanceof \BookStack\Entities\Models\Book)
            <div class="pl-l block inline">
                <div class="text-chapter">
                    @icon('chapter') {{ trans_choice('entities.x_chapters', $deletion->deletable->chapters()->withTrashed()->count()) }}
                </div>
            </div>
        @endif
        @if($deletion->deletable instanceof \BookStack\Entities\Models\Book || $deletion->deletable instanceof \BookStack\Entities\Models\Chapter)
            <div class="pl-l block inline">
                <div class="text-page">
                    @icon('page') {{ trans_choice('entities.x_pages', $deletion->deletable->pages()->withTrashed()->count()) }}
                </div>
            </div>
        @endif
    </div>
    <div class="flex-2 px-m py-xs min-width-m">
        @if($deletion->deletable->getParent())
            <strong class="hide-over-l">{{ trans('settings.recycle_bin_deleted_parent') }}:<br></strong>
            <div class="flex-container-row items-center">
                <span role="presentation" class="flex-none icon text-{{$deletion->deletable->getParent()->getType()}}">@icon($deletion->deletable->getParent()->getType())</span>
                <div class="text-{{ $deletion->deletable->getParent()->getType() }}">
                    {{ $deletion->deletable->getParent()->name }}
                </div>
            </div>
        @endif
    </div>
    <div class="flex-2 px-m py-xs flex-container-row items-center min-width-m">
        <div><strong class="hide-over-l">{{ trans('settings.recycle_bin_deleted_by') }}:<br></strong>@include('settings.parts.table-user', ['user' => $deletion->deleter, 'user_id' => $deletion->deleted_by])</div>
    </div>
    <div class="flex px-m py-xs min-width-s"><strong class="hide-over-l">{{ trans('settings.recycle_bin_deleted_at') }}:<br></strong>{{ $deletion->created_at }}</div>
    <div class="flex px-m py-xs text-m-right min-width-s">
        <div component="dropdown" class="dropdown-container">
            <button type="button" refs="dropdown@toggle" class="button outline">{{ trans('common.actions') }}</button>
            <ul refs="dropdown@menu" class="dropdown-menu">
                <li><a class="text-item" href="{{ $deletion->getUrl('/restore') }}">{{ trans('settings.recycle_bin_restore') }}</a></li>
                <li><a class="text-item" href="{{ $deletion->getUrl('/destroy') }}">{{ trans('settings.recycle_bin_permanently_delete') }}</a></li>
            </ul>
        </div>
    </div>
</div>