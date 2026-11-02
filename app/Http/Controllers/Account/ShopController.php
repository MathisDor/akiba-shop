<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Services\ListingImageStore;
use App\Support\Provinces;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** The member's public storefront: photos, description, location, delivery and hours. */
class ShopController extends Controller
{
    public function __construct(private ListingImageStore $images) {}

    public function edit(): View
    {
        return view('account.shop', [
            'user' => Auth::guard('web')->user(),
            'provinces' => Provinces::options(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::guard('web')->user();

        $image = ['nullable', 'file', 'max:'.config('marketplace.max_image_kb'), function (string $attribute, mixed $value, Closure $fail) {
            if (! ListingImageStore::isImage($value)) {
                $fail('Choisissez une image JPEG, PNG ou WebP.');
            }
        }];

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'province' => ['nullable', Rule::in(array_keys(config('marketplace.provinces')))],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:150'],
            'delivery_note' => ['nullable', 'string', 'max:200'],
            'opening_hours' => ['nullable', 'string', 'max:120'],
            'avatar' => $image,
            'cover' => $image,
        ], [
            'name.required' => 'Donnez un nom à votre boutique.',
            'avatar.max' => 'La photo doit faire moins de '.intdiv(config('marketplace.max_image_kb'), 1024).' Mo.',
            'cover.max' => 'La photo doit faire moins de '.intdiv(config('marketplace.max_image_kb'), 1024).' Mo.',
        ]);

        $user->fill([
            ...collect($validated)->except(['avatar', 'cover'])->map(fn ($value) => is_string($value) ? trim($value) : $value)->all(),
            'delivers' => $request->boolean('delivers'),
        ]);

        // Photos: replace with a new upload, or remove on request; the old file is deleted either way.
        foreach (['avatar' => 400, 'cover' => 1600] as $field => $maxDimension) {
            $column = $field.'_path';
            if ($request->hasFile($field)) {
                $this->images->deletePath($user->$column);
                $user->$column = $this->images->storeResized($request->file($field), 'shops/'.$user->id, $maxDimension);
            } elseif ($request->boolean('remove_'.$field)) {
                $this->images->deletePath($user->$column);
                $user->$column = null;
            }
        }

        $user->save();

        return redirect()->route('account.shop.edit')->with('status', 'Votre boutique est à jour.');
    }
}
