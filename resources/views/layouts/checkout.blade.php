<x-layouts.checkout :title="$title ?? null" :step="$step ?? 'informations'" :back-url="$backUrl ?? null">
    {{ $slot }}
</x-layouts.checkout>
