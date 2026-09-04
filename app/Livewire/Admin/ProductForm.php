<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Collection as CollectionModel;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Size;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ProductForm extends Component
{
    use WithFileUploads;

    public ?Product $product = null;

    public int $step = 1;

    public int $maxStepReached = 1;

    public string $name = '';

    public string $sku = '';

    public string $description = '';

    public ?int $categoryId = null;

    public ?int $collectionId = null;

    public string $brand = '';

    public string $material = '';

    public string $fit = '';

    public string $season = '';

    public string $careInstructions = '';

    public string $gender = 'unisexe';

    public int $basePrice = 0;

    public bool $isActive = true;

    /** @var array<int, TemporaryUploadedFile> */
    public array $newImages = [];

    public array $existingImages = [];

    public array $selectedColorIds = [];

    public array $selectedSizeIds = [];

    /** @var array<string, array{stock:int, price_override:?int, variant_id:?int, sku?:string}> */
    public array $variantMatrix = [];

    /** @var array<int, array<int, string>> color_id => list of 'existing:{imageId}' | 'new:{index}' */
    public array $colorImages = [];

    public bool $hasNoSizes = false;

    public string $feedback = '';

    public function mount(?Product $product = null): void
    {
        if ($product && $product->exists) {
            $this->product = $product;
            $this->name = $product->name;
            $this->sku = $product->sku;
            $this->description = (string) $product->description;
            $this->categoryId = $product->category_id;
            $this->collectionId = $product->collection_id;
            $this->brand = (string) $product->brand;
            $this->material = (string) $product->material;
            $this->fit = (string) $product->fit;
            $this->season = (string) $product->season;
            $this->careInstructions = (string) $product->care_instructions;
            $this->gender = $product->gender;
            $this->basePrice = $product->base_price;
            $this->isActive = $product->is_active;

            $this->existingImages = $product->images->map(fn ($img) => [
                'id' => $img->id,
                'path' => $img->path,
                'url' => Storage::disk('public')->url($img->path),
                'sort_order' => $img->sort_order,
                'is_primary' => $img->is_primary,
            ])->sortBy('sort_order')->values()->all();

            $this->selectedColorIds = $product->variants->pluck('color_id')->filter()->unique()->values()->all();
            $this->selectedSizeIds = $product->variants->pluck('size_id')->filter()->unique()->values()->all();
            $this->hasNoSizes = $product->variants->isNotEmpty() && $product->variants->every(fn ($v) => $v->size_id === null);

            foreach ($product->variants as $variant) {
                $key = $this->matrixKey($variant->color_id, $variant->size_id);
                $this->variantMatrix[$key] = [
                    'stock' => $variant->stock,
                    'price_override' => $variant->price_override,
                    'variant_id' => $variant->id,
                    'sku' => $variant->sku,
                ];
            }

            foreach ($product->images as $img) {
                if ($img->color_id) {
                    $this->colorImages[$img->color_id][] = 'existing:'.$img->id;
                }
            }
        }
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:100',
            'description' => 'nullable|string',
            'categoryId' => 'required|exists:categories,id',
            'gender' => 'required|in:homme,femme,unisexe',
            'basePrice' => 'required|integer|min:1',
            'newImages.*' => 'nullable|image|max:8192',
        ];
    }

    protected function messages(): array
    {
        return [
            'basePrice.required' => 'Le prix de base est obligatoire.',
            'basePrice.min' => 'Le prix de base doit être supérieur à 0.',
            'newImages.*.image' => 'Le fichier doit être une image.',
            'newImages.*.max' => 'Chaque image doit faire moins de 8 Mo.',
        ];
    }

    public function updatedNewImages(): void
    {
        $this->validate([
            'newImages.*' => 'image|max:8192',
        ]);
    }

    protected function stepFields(int $step): array
    {
        return match ($step) {
            1 => ['name', 'description', 'categoryId', 'gender'],
            2 => ['basePrice'],
            default => [],
        };
    }

    public function nextStep(): void
    {
        $this->feedback = '';

        $stepRules = Arr::only($this->rules(), $this->stepFields($this->step));

        if ($stepRules !== []) {
            $this->validate($stepRules);
        }

        if ($this->step === 1 && $this->sku === '') {
            $this->sku = $this->generateProductSku();
        }

        if ($this->step === 4) {
            if (empty($this->selectedColorIds)) {
                $this->feedback = 'Sélectionnez au moins une couleur.';

                return;
            }

            if (! $this->hasNoSizes && empty($this->selectedSizeIds)) {
                $this->feedback = 'Sélectionnez au moins une taille, ou cochez "Article à taille unique".';

                return;
            }
        }

        $this->step = min(5, $this->step + 1);
        $this->maxStepReached = max($this->maxStepReached, $this->step);
    }

    public function prevStep(): void
    {
        $this->feedback = '';
        $this->step = max(1, $this->step - 1);
    }

    public function goToStep(int $target): void
    {
        if ($target >= 1 && $target <= $this->maxStepReached) {
            $this->feedback = '';
            $this->step = $target;
        }
    }

    private function matrixKey(?int $colorId, ?int $sizeId): string
    {
        return ($colorId ?? 'none').'_'.($sizeId ?? 'none');
    }

    public function updatedSelectedColorIds(): void
    {
        $this->syncMatrix();
    }

    public function updatedSelectedSizeIds(): void
    {
        $this->syncMatrix();
    }

    public function updatedHasNoSizes(): void
    {
        $this->syncMatrix();
    }

    private function syncMatrix(): void
    {
        $sizeIds = $this->hasNoSizes ? [null] : $this->selectedSizeIds;

        $newMatrix = [];
        foreach ($this->selectedColorIds as $colorId) {
            foreach ($sizeIds as $sizeId) {
                $key = $this->matrixKey($colorId, $sizeId);
                $newMatrix[$key] = $this->variantMatrix[$key] ?? ['stock' => 10, 'price_override' => null, 'variant_id' => null];
            }
        }

        $this->variantMatrix = $newMatrix;
    }

    /**
     * Toggle a gallery image on/off for a color. An image belongs to at
     * most one color, so selecting it here removes it from any other
     * color it was previously assigned to.
     */
    public function toggleColorImage(int $colorId, string $selection): void
    {
        $wasSelectedForThisColor = in_array($selection, $this->colorImages[$colorId] ?? [], true);

        foreach ($this->colorImages as $cId => $selections) {
            $this->colorImages[$cId] = array_values(array_diff($selections, [$selection]));
        }

        if (! $wasSelectedForThisColor) {
            $this->colorImages[$colorId][] = $selection;
        }
    }

    private function generateVariantSku(?string $colorName, ?string $sizeName): string
    {
        $base = Str::upper(Str::slug($this->sku ?: 'SKU', ''));
        $colorPart = $colorName ? Str::upper(Str::slug($colorName, '')) : null;
        $sizePart = $sizeName ? Str::upper(Str::slug($sizeName, '')) : null;

        return collect([$base, $colorPart, $sizePart])->filter()->implode('-');
    }

    public function displaySku(?int $colorId, ?int $sizeId, ?string $colorName, ?string $sizeName): string
    {
        $key = $this->matrixKey($colorId, $sizeId);

        return $this->variantMatrix[$key]['sku'] ?? $this->generateVariantSku($colorName, $sizeName);
    }

    private function generateProductSku(): string
    {
        $base = Str::upper(Str::slug($this->name, ''));
        $base = $base !== '' ? Str::limit($base, 20, '') : 'PROD';

        $candidate = $base;
        $suffix = 1;

        while (
            Product::where('sku', $candidate)
                ->when($this->product, fn ($q) => $q->where('id', '!=', $this->product->id))
                ->exists()
        ) {
            $suffix++;
            $candidate = $base.'-'.$suffix;
        }

        return $candidate;
    }

    public function removeExistingImage(int $imageId): void
    {
        $image = ProductImage::find($imageId);

        if ($image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        $this->existingImages = collect($this->existingImages)->reject(fn ($img) => $img['id'] === $imageId)->values()->all();

        $this->colorImages = collect($this->colorImages)
            ->map(fn ($selections) => array_values(array_diff($selections, ['existing:'.$imageId])))
            ->all();
    }

    public function setPrimaryImage(int $imageId): void
    {
        $this->existingImages = collect($this->existingImages)->map(function ($img) use ($imageId) {
            $img['is_primary'] = $img['id'] === $imageId;

            return $img;
        })->all();
    }

    public function moveImage(int $index, int $direction): void
    {
        $images = $this->existingImages;
        $target = $index + $direction;

        if (! isset($images[$index]) || ! isset($images[$target])) {
            return;
        }

        [$images[$index], $images[$target]] = [$images[$target], $images[$index]];
        $this->existingImages = $images;
    }

    public function save()
    {
        if ($this->sku === '') {
            $this->sku = $this->generateProductSku();
        }

        $this->validate();

        if (empty($this->selectedColorIds)) {
            $this->feedback = 'Sélectionnez au moins une couleur.';

            return null;
        }

        if (! $this->hasNoSizes && empty($this->selectedSizeIds)) {
            $this->feedback = 'Sélectionnez au moins une taille, ou cochez "Article à taille unique".';

            return null;
        }

        DB::transaction(function () {
            $data = [
                'name' => $this->name,
                'slug' => Str::slug($this->name),
                'sku' => $this->sku,
                'description' => $this->description,
                'category_id' => $this->categoryId,
                'collection_id' => $this->collectionId,
                'brand' => $this->brand ?: null,
                'material' => $this->material ?: null,
                'fit' => $this->fit ?: null,
                'season' => $this->season ?: null,
                'care_instructions' => $this->careInstructions ?: null,
                'gender' => $this->gender,
                'base_price' => $this->basePrice,
                'is_active' => $this->isActive,
                'published_at' => $this->product?->published_at ?? now(),
            ];

            $adminId = Auth::guard('admin')->id();

            if ($this->product) {
                $this->product->update($data);
                $this->product->forceFill(['updated_by' => $adminId])->save();
                $product = $this->product;
            } else {
                $product = Product::create($data);
                $product->forceFill(['created_by' => $adminId, 'updated_by' => $adminId])->save();
            }

            // Images: persist reordering / primary flag for existing, then store new uploads.
            foreach ($this->existingImages as $i => $img) {
                ProductImage::where('id', $img['id'])->update([
                    'sort_order' => $i,
                    'is_primary' => $img['is_primary'] ?? false,
                ]);
            }

            $startOrder = count($this->existingImages);
            $hasPrimary = collect($this->existingImages)->contains(fn ($img) => $img['is_primary'] ?? false);

            $newImageIdByIndex = [];
            foreach ($this->newImages as $i => $file) {
                $path = $file->store('products', 'public');
                $image = ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'sort_order' => $startOrder + $i,
                    'is_primary' => ! $hasPrimary && $i === 0,
                ]);
                $newImageIdByIndex[$i] = $image->id;
            }

            // Resolve every selected gallery image per color into a concrete image id,
            // then tag each ProductImage with its assigned color (or none).
            $colorIdByImageId = [];
            foreach ($this->colorImages as $colorId => $selections) {
                foreach ($selections as $selection) {
                    if (str_starts_with($selection, 'existing:')) {
                        $imageId = (int) substr($selection, 9);
                    } elseif (str_starts_with($selection, 'new:')) {
                        $index = (int) substr($selection, 4);
                        $imageId = $newImageIdByIndex[$index] ?? null;
                    } else {
                        continue;
                    }

                    if ($imageId) {
                        $colorIdByImageId[$imageId] = (int) $colorId;
                    }
                }
            }

            foreach (ProductImage::where('product_id', $product->id)->get() as $image) {
                $image->update(['color_id' => $colorIdByImageId[$image->id] ?? null]);
            }

            // Variants
            $keptVariantIds = [];
            $sizeIds = $this->hasNoSizes ? [null] : $this->selectedSizeIds;

            $colorNames = Color::whereIn('id', $this->selectedColorIds)->pluck('name', 'id');
            $sizeNames = $this->hasNoSizes ? collect() : Size::whereIn('id', $this->selectedSizeIds)->pluck('name', 'id');

            foreach ($this->selectedColorIds as $colorId) {
                foreach ($sizeIds as $sizeId) {
                    $key = $this->matrixKey($colorId, $sizeId);
                    $row = $this->variantMatrix[$key] ?? ['stock' => 10, 'price_override' => null, 'variant_id' => null];

                    if ($row['variant_id']) {
                        $variant = ProductVariant::find($row['variant_id']);
                        $variant->update([
                            'stock' => $row['stock'],
                            'price_override' => $row['price_override'] ?: null,
                        ]);
                        $keptVariantIds[] = $variant->id;
                    } else {
                        $variant = ProductVariant::create([
                            'product_id' => $product->id,
                            'color_id' => $colorId,
                            'size_id' => $sizeId,
                            'sku' => $this->generateVariantSku(
                                $colorNames[$colorId] ?? null,
                                $sizeId ? ($sizeNames[$sizeId] ?? null) : null
                            ),
                            'stock' => $row['stock'],
                            'price_override' => $row['price_override'] ?: null,
                        ]);
                        $keptVariantIds[] = $variant->id;
                    }
                }
            }

            $product->variants()->whereNotIn('id', $keptVariantIds)->delete();
        });

        session()->flash('status', $this->product ? 'Produit mis à jour.' : 'Produit créé.');

        return redirect()->route('admin.products.index');
    }

    public function render()
    {
        return view('livewire.admin.product-form', [
            'categories' => Category::orderBy('gender')->orderBy('name')->get(),
            'collections' => CollectionModel::orderBy('name')->get(),
            'colors' => Color::orderBy('name')->get(),
            'sizes' => Size::orderBy('sort_order')->get(),
        ]);
    }
}
