@php
    $coordinator = $coordinator ?? false;
    $waka = $waka ?? false;
@endphp

@forelse($assignments as $assignment)
    <p class="mb-1 last:mb-0 font-medium text-slate-700">
        @if($coordinator)<i class="bi bi-star-fill mr-1 text-amber-500"></i>@endif
        @if($waka)<i class="bi bi-shield-check mr-1 text-emerald-600"></i>@endif
        {{ $assignment->user?->name ?? 'Belum ditugaskan' }}
    </p>
@empty
    <span class="text-slate-400">—</span>
@endforelse
