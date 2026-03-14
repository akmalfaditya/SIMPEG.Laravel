@props(['text'])

<span class="relative group inline-flex items-center ml-1 cursor-help">
    <span class="inline-flex items-center justify-center w-4 h-4 rounded-full bg-slate-200 text-slate-500 text-[10px] font-bold">?</span>
    <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2.5 py-1.5 bg-slate-800 text-white text-xs rounded-lg whitespace-nowrap opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 pointer-events-none z-50 shadow-lg">
        {{ $text }}
        <span class="absolute top-full left-1/2 -translate-x-1/2 -mt-px border-4 border-transparent border-t-slate-800"></span>
    </span>
</span>
