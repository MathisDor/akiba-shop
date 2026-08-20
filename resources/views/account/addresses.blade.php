<x-layouts.app active="account" title="Mes Adresses — AkibaShop">
    <div
        class="max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop py-8"
        x-data="{
            modalOpen: false,
            mode: 'create',
            action: '{{ route('account.addresses.store') }}',
            form: { label: 'Domicile', full_name: '', phone: '', province: 'Estuaire', city: 'Libreville', commune: '', detailed_address: '', landmark: '' },
            openCreate() {
                this.mode = 'create';
                this.action = '{{ route('account.addresses.store') }}';
                this.form = { label: 'Domicile', full_name: '', phone: '', province: 'Estuaire', city: 'Libreville', commune: '', detailed_address: '', landmark: '' };
                this.modalOpen = true;
            },
            openEdit(address, url) {
                this.mode = 'edit';
                this.action = url;
                this.form = { ...address };
                this.modalOpen = true;
            }
        }"
    >
        @session('status')
            <div class="mb-6 rounded-lg bg-primary-container/10 text-primary px-4 py-3 font-body-md text-body-md text-center">
                {{ $value }}
            </div>
        @endsession

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
            <p class="font-body-lg text-body-lg text-on-surface-variant max-w-2xl">
                Gérez vos adresses de livraison pour faciliter vos prochaines commandes.
            </p>
            <button @click="openCreate()" type="button" class="bg-primary text-on-primary px-8 py-4 rounded-full font-label-sm text-label-sm uppercase hover:bg-on-surface-variant transition-colors flex items-center gap-2">
<x-icon name="add" class="text-[18px]" />
                Ajouter une adresse
            </button>
        </div>

        @if($addresses->isEmpty())
            <div class="flex flex-col items-center justify-center text-center py-20">
                <div class="w-24 h-24 rounded-full bg-surface-container-high flex items-center justify-center mb-6">
                    <x-icon name="location_off" class="text-4xl text-on-surface-variant" />
                </div>
                <h2 class="font-headline-md text-headline-md text-primary mb-2">Aucune adresse enregistrée</h2>
                <p class="font-body-md text-body-md text-on-surface-variant mb-8 max-w-sm">Ajoutez une adresse pour accélérer vos prochaines commandes.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($addresses as $address)
                    <div class="bg-surface-container-lowest rounded-xl p-6 shadow-[0_10px_40px_rgba(0,0,0,0.04)] relative overflow-hidden border {{ $address->is_default ? 'border-surface-variant' : 'border-transparent' }}">
                        @if($address->is_default)
                            <div class="absolute top-0 right-0 bg-primary text-on-primary px-3 py-1 text-[10px] font-bold uppercase tracking-wider rounded-bl-lg">Par défaut</div>
                        @endif
                        <div class="flex items-start gap-3 mb-4 mt-2">
                            <div class="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center text-primary">
<x-icon :name="$address->label === 'Bureau' ? 'work' : 'home'" />
                            </div>
                            <div>
                                <h3 class="font-headline-md text-headline-md text-primary">{{ $address->label }}</h3>
                                <p class="font-label-sm text-label-sm text-on-surface-variant">{{ $address->full_name }}</p>
                            </div>
                        </div>
                        <div class="space-y-2 mb-6">
                            <p class="font-body-md text-body-md flex items-start gap-2 text-on-surface">
<x-icon name="location_on" class="text-[20px] text-outline mt-1" />
                                <span>{{ $address->detailed_address }}@if($address->commune), {{ $address->commune }}@endif<br />{{ $address->city }}, {{ $address->province }}</span>
                            </p>
                            <p class="font-body-md text-body-md flex items-center gap-2 text-on-surface">
<x-icon name="phone" class="text-[20px] text-outline" />
                                {{ $address->phone }}
                            </p>
                        </div>
                        <div class="flex items-center justify-between pt-4 border-t border-surface-variant">
                            <button
                                type="button"
                                @click="openEdit(@js($address->only(['label','full_name','phone','province','city','commune','detailed_address','landmark'])), '{{ route('account.addresses.update', $address) }}')"
                                class="text-primary font-label-sm text-label-sm uppercase hover:text-secondary transition-colors flex items-center gap-1"
                            >
<x-icon name="edit" class="text-[16px]" /> Modifier
                            </button>

                            <div class="flex items-center gap-4">
                                @unless($address->is_default)
                                    <form method="POST" action="{{ route('account.addresses.default', $address) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-on-surface-variant font-label-sm text-label-sm uppercase hover:text-primary transition-colors">Définir par défaut</button>
                                    </form>
                                @endunless
                                <form method="POST" action="{{ route('account.addresses.destroy', $address) }}" onsubmit="return confirm('Supprimer cette adresse ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-error font-label-sm text-label-sm uppercase hover:opacity-70 transition-opacity flex items-center gap-1">
<x-icon name="delete" class="text-[16px]" /> Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Modal --}}
        <div x-show="modalOpen" x-cloak class="fixed inset-0 bg-inverse-surface/40 backdrop-blur-sm z-50 flex items-center justify-center p-4" @click.self="modalOpen = false">
            <div class="bg-surface-container-lowest rounded-xl shadow-2xl w-full max-w-lg p-6 md:p-8 max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-headline-md text-headline-md text-primary" x-text="mode === 'create' ? 'Ajouter une adresse' : 'Modifier l\'adresse'"></h3>
                    <button @click="modalOpen = false" class="text-on-surface-variant hover:text-primary"><x-icon name="close" /></button>
                </div>

                <form :action="action" method="POST" class="flex flex-col gap-4">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                    <template x-if="mode === 'edit'"><input type="hidden" name="_method" value="PUT" /></template>

                    <div>
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Libellé</label>
                        <input x-model="form.label" name="label" type="text" placeholder="Domicile, Bureau..." class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                    </div>
                    <div>
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Nom complet</label>
                        <input x-model="form.full_name" name="full_name" type="text" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                    </div>
                    <div>
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Téléphone</label>
                        <input x-model="form.phone" name="phone" type="text" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="font-label-sm text-label-sm text-on-surface-variant">Province</label>
                            <select x-model="form.province" name="province" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md">
                                <option>Estuaire</option>
                                <option>Haut-Ogooué</option>
                                <option>Moyen-Ogooué</option>
                            </select>
                        </div>
                        <div>
                            <label class="font-label-sm text-label-sm text-on-surface-variant">Ville</label>
                            <input x-model="form.city" name="city" type="text" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                        </div>
                    </div>
                    <div>
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Quartier</label>
                        <input x-model="form.commune" name="commune" type="text" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                    </div>
                    <div>
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Adresse détaillée</label>
                        <input x-model="form.detailed_address" name="detailed_address" type="text" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                    </div>
                    <div>
                        <label class="font-label-sm text-label-sm text-on-surface-variant">Point de repère</label>
                        <input x-model="form.landmark" name="landmark" type="text" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                    </div>

                    <button type="submit" class="mt-4 w-full bg-primary text-on-primary py-3 rounded-full font-label-sm text-label-sm uppercase tracking-widest hover:opacity-90 transition-opacity">
                        Enregistrer
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
