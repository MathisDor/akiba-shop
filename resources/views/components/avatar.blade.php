@props(['user', 'size' => 'w-10 h-10 text-[15px]'])

{{-- The member's shop photo, or their initial on the brand tint when they have none --}}
@if($user?->avatarUrl())
    <img src="{{ $user->avatarUrl() }}" alt="" loading="lazy" {{ $attributes->merge(['class' => $size.' shrink-0 rounded-full object-cover bg-surface-container-low']) }} />
@else
    <span {{ $attributes->merge(['class' => $size.' shrink-0 rounded-full bg-primary-fixed text-on-primary-fixed-variant flex items-center justify-center font-extrabold']) }} aria-hidden="true">{{ mb_strtoupper(mb_substr($user?->name ?? '?', 0, 1)) }}</span>
@endif
