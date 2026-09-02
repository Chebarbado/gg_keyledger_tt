@props([
    'title',
    'showAll' => false,
])

<div class="flex items-center justify-between gap-4">
    <h2 class="text-xl font-bold text-heading">{{ $title }}</h2>
    @if ($showAll)
        <a href="#" class="rounded-[10px] bg-page px-5 py-1.5 text-[13px] font-bold text-heading hover:bg-[#e8eaed]">
            Показать все
        </a>
    @endif
</div>
