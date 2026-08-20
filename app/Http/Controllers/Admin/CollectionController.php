<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CollectionController extends Controller
{
    public function index(): View
    {
        $collections = Collection::withCount('products')->orderByDesc('created_at')->get();

        return view('admin.collections.index', compact('collections'));
    }

    public function create(): View
    {
        return view('admin.collections.form', ['collection' => new Collection]);
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['slug'] = Str::slug($validated['name']);

        Collection::create($validated);

        return redirect()->route('admin.collections.index')->with('status', 'Collection créée.');
    }

    public function edit(Collection $collection): View
    {
        return view('admin.collections.form', compact('collection'));
    }

    public function update(Request $request, Collection $collection): RedirectResponse
    {
        $validated = $request->validate($this->rules());
        $validated['is_active'] = $request->boolean('is_active', true);

        $collection->update($validated);

        return redirect()->route('admin.collections.index')->with('status', 'Collection mise à jour.');
    }

    public function destroy(Collection $collection): RedirectResponse
    {
        $collection->delete();

        return redirect()->route('admin.collections.index')->with('status', 'Collection supprimée.');
    }
}
