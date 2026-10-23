<x-layouts.admin active="categories" title="Catégories">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5">
        <p class="text-[14px] text-on-surface-variant">{{ $categories->count() }} catégories principales</p>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm"><x-icon name="add" class="text-[18px]" /> Nouvelle catégorie</a>
    </div>

    @session('status')
        <div class="mb-4 rounded-xl bg-primary-fixed text-on-primary-fixed px-4 py-3 text-[14px]">{{ $value }}</div>
    @endsession
    @session('error')
        <div class="mb-4 rounded-xl bg-error-container text-on-error-container px-4 py-3 text-[14px]">{{ $value }}</div>
    @endsession

    <div class="flex flex-col gap-3">
        @foreach($categories as $category)
            <section class="card p-4">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-xl bg-primary-fixed text-primary flex items-center justify-center"><x-icon :name="$category->icon ?? 'package'" class="text-[20px]" /></span>
                    <div class="flex-1 min-w-0">
                        <p class="text-[15px] font-bold text-on-surface">{{ $category->name }} @unless($category->is_active)<span class="text-[11px] font-semibold text-on-surface-variant">(inactive)</span>@endunless</p>
                        <p class="text-[12px] text-on-surface-variant">{{ $category->listings_count + $category->children->sum('listings_count') }} annonces · /{{ $category->slug }}</p>
                    </div>
                    <a href="{{ route('admin.categories.edit', $category) }}" class="text-[13px] font-semibold text-secondary hover:underline">Modifier</a>
                </div>
                @if($category->children->isNotEmpty())
                    <ul class="mt-3 pl-[52px] flex flex-wrap gap-2">
                        @foreach($category->children as $child)
                            <li>
                                <a href="{{ route('admin.categories.edit', $child) }}" class="inline-flex items-center gap-1 rounded-full bg-surface-container-low px-3 py-1 text-[13px] hover:bg-surface-container {{ $child->is_active ? '' : 'line-through opacity-60' }}">
                                    {{ $child->name }} <span class="text-on-surface-variant">{{ $child->listings_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endforeach
    </div>
</x-layouts.admin>
