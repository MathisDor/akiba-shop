<x-layouts.app :active="$active ?? null" :title="$title ?? null" :description="$description ?? null" :hide-footer="$hideFooter ?? false">
    {{ $slot }}
</x-layouts.app>
