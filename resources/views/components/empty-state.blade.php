@props(['title' => 'Belum Ada Data', 'message' => 'Data belum tersedia.', 'colspan' => 1])

<tr>
    <td colspan="{{ $colspan }}" class="px-4 py-10 text-center">
        <div class="flex flex-col items-center gap-2">
            <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <p class="text-sm font-medium text-slate-500">{{ $title }}</p>
            <p class="text-xs text-slate-400 max-w-xs">{{ $message }}</p>
        </div>
    </td>
</tr>
