<div component="back-to-top" class="back-to-top-container">
    <svg refs="back-to-top@progress" class="back-to-top-progress" width="100%" height="100%">
        <path
            refs="back-to-top@progress-path"
            d=""
            pathLength="100"
            stroke-dasharray="0 100"
        />
    </svg>
    <button refs="back-to-top@button" class="back-to-top print-hidden">
        <div class="inner">
            @icon('chevron-up') <span>{{ trans('common.back_to_top') }}</span>
        </div>
    </button>
</div>