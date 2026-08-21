<div class="w-full overflow-x-auto rounded-lg border border-neutral-200 bg-white shadow-xs">
    <table class="w-full text-left text-body text-neutral-700 whitespace-nowrap">
        @if(isset($head))
            <thead class="bg-neutral-50 text-caption font-semibold text-neutral-500 uppercase tracking-wide border-b border-neutral-200">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
        @endif
        
        <tbody class="divide-y divide-neutral-100">
            @if(isset($empty) && (!isset($rows) || count($rows) === 0))
                <tr>
                    <td colspan="100%" class="py-12 px-4 text-center">
                        <div class="flex flex-col items-center justify-center text-neutral-500">
                            {{ $empty }}
                        </div>
                    </td>
                </tr>
            @else
                {{ $slot }}
            @endif
        </tbody>
    </table>
    
    @if(isset($pagination))
        <div class="px-4 py-3 border-t border-neutral-200 bg-white">
            {{ $pagination }}
        </div>
    @endif
</div>
