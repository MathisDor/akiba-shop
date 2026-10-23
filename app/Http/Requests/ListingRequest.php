<?php

namespace App\Http\Requests;

use App\Enums\ListingCondition;
use App\Models\Listing;
use App\Models\ListingCategory;
use App\Services\ListingImageStore;
use App\Support\Phone;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $price = preg_replace('/\D+/', '', (string) $this->input('price'));

        $this->merge([
            'price' => $price === '' ? null : $price,
            'contact_phone' => Phone::normalize($this->input('contact_phone'), $this->input('contact_phone_country') ?: $this->input('country_code')) ?? $this->input('contact_phone'),
            'city' => trim((string) $this->input('city')),
            'is_negotiable' => $this->boolean('is_negotiable'),
            'accepts_whatsapp' => $this->boolean('accepts_whatsapp'),
        ]);
    }

    public function rules(): array
    {
        /** @var Listing|null $listing */
        $listing = $this->route('listing');
        $keptImages = $listing ? $listing->images()->whereNotIn('id', (array) $this->input('remove_images', []))->count() : 0;

        return [
            'title' => ['required', 'string', 'min:5', 'max:100'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'listing_category_id' => ['required', 'integer', function (string $attribute, mixed $value, Closure $fail) {
                $category = ListingCategory::active()->withCount('children')->find($value);
                if (! $category) {
                    $fail('Choisissez une catégorie.');
                } elseif ($category->children_count > 0) {
                    $fail('Choisissez une sous-catégorie.');
                }
            }],
            'price' => ['nullable', 'integer', 'min:0', 'max:99999999999'],
            'is_negotiable' => ['boolean'],
            'condition' => ['nullable', Rule::enum(ListingCondition::class)],
            'country_code' => ['required', Rule::in(array_keys(config('countries')))],
            'city' => ['required', 'string', 'min:2', 'max:100'],
            'contact_phone' => ['required', 'regex:/^\+\d{8,15}$/'],
            'accepts_whatsapp' => ['boolean'],
            'specs' => ['nullable', 'array'],
            'specs.*' => ['nullable', 'string', 'max:100'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer'],
            'photos' => ['nullable', 'array', 'max:'.max(0, config('marketplace.max_images') - $keptImages)],
            'photos.*' => ['file', 'max:'.config('marketplace.max_image_kb'), function (string $attribute, mixed $value, Closure $fail) {
                if (! ListingImageStore::isImage($value)) {
                    $fail('Chaque photo doit être une image JPEG, PNG ou WebP.');
                }
            }],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Donnez un titre à votre annonce.',
            'title.min' => 'Le titre doit faire au moins 5 caractères.',
            'description.required' => 'Décrivez ce que vous proposez.',
            'description.min' => 'La description doit faire au moins 20 caractères.',
            'listing_category_id.required' => 'Choisissez une catégorie.',
            'country_code.required' => 'Choisissez un pays.',
            'city.required' => 'Indiquez la ville.',
            'contact_phone.required' => 'Indiquez un numéro de téléphone.',
            'contact_phone.regex' => 'Numéro de téléphone invalide pour ce pays.',
            'photos.max' => 'Vous pouvez mettre au maximum '.config('marketplace.max_images').' photos par annonce.',
            'photos.*.max' => 'Chaque photo doit faire moins de '.intdiv(config('marketplace.max_image_kb'), 1024).' Mo.',
            'price.integer' => 'Le prix doit être un nombre entier.',
        ];
    }

    /** Validated attributes ready for Listing::fill(), with specs restricted to the category's fields. */
    public function listingAttributes(): array
    {
        $data = $this->safe()->except(['photos', 'remove_images', 'specs', 'contact_phone_country']);
        $category = ListingCategory::with('parent')->find($data['listing_category_id']);

        $allowed = collect($category->fields())->pluck('key');
        $data['specs'] = collect((array) $this->validated('specs', []))
            ->only($allowed)
            ->map(fn ($value) => is_string($value) ? trim($value) : $value)
            ->filter(fn ($value) => filled($value))
            ->all() ?: null;

        if ($data['price'] === null) {
            $data['is_negotiable'] = false;
        }

        return $data;
    }
}
