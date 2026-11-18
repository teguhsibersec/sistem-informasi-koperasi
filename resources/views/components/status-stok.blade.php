@props(['stok', 'min'])
@if ($stok <= 0)
    <span class="inline-block rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">Habis</span>
@elseif ($stok <= $min)
    <span class="inline-block rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-medium text-orange-700">Menipis</span>
@else
    <span class="inline-block rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">Aman</span>
@endif
