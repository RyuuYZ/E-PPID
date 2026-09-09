@if ($paginator->hasPages() || $paginator->total() > 0)
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 w-full">
        <!-- Total Pill Badge (Left) -->
        <div class="flex items-center">
            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-slate-100/90 border border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider shadow-2xs">
                <span>TOTAL:</span>
                <span class="font-extrabold text-slate-800 text-xs">{{ number_format($paginator->total(), 0, ',', '.') }}</span>
                <span>{{ $resourceName ?? 'DATA' }}</span>
            </span>
        </div>

        <!-- Navigation Controls (Right) -->
        <div class="flex items-center gap-2.5 flex-wrap justify-center sm:justify-end">
            <!-- Prev Button -->
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-full bg-slate-50 border border-slate-200/60 text-slate-400 text-xs font-semibold cursor-not-allowed select-none opacity-60">
                    <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                    <span>Prev</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" 
                   class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-full bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 text-xs font-semibold shadow-2xs transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[16px]">chevron_left</span>
                    <span>Prev</span>
                </a>
            @endif

            <!-- Jump to Page Box (KE HAL: [ 1 ] / X Go ->) -->
            <div class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3.5 py-1 shadow-2xs">
                <form method="GET" action="{{ url()->current() }}" class="flex items-center gap-2 m-0">
                    @foreach(request()->except('page') as $key => $val)
                        @if(is_array($val))
                            @foreach($val as $subVal)
                                <input type="hidden" name="{{ $key }}[]" value="{{ $subVal }}">
                            @endforeach
                        @else
                            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                        @endif
                    @endforeach

                    <span class="text-slate-500 text-[11px] font-bold uppercase tracking-wider">KE HAL:</span>
                    <input type="number" 
                           name="page" 
                           min="1" 
                           max="{{ max(1, $paginator->lastPage()) }}" 
                           value="{{ $paginator->currentPage() }}" 
                           class="w-12 text-center py-0.5 px-1 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500 shadow-2xs">
                    <span class="text-slate-400 font-medium text-xs">/ {{ max(1, $paginator->lastPage()) }}</span>

                    <button type="submit" 
                            class="inline-flex items-center gap-1 bg-[#00875a] hover:bg-[#00714c] text-white px-2.5 py-1 rounded-full text-xs font-bold transition-all shadow-2xs cursor-pointer ml-1">
                        <span>Go</span>
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </button>
                </form>
            </div>

            <!-- Next Button -->
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" 
                   class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-full bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:bg-slate-50 text-xs font-semibold shadow-2xs transition-colors cursor-pointer">
                    <span>Next</span>
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                </a>
            @else
                <span class="inline-flex items-center gap-1 px-3.5 py-1.5 rounded-full bg-slate-50 border border-slate-200/60 text-slate-400 text-xs font-semibold cursor-not-allowed select-none opacity-60">
                    <span>Next</span>
                    <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                </span>
            @endif
        </div>
    </div>
@endif
