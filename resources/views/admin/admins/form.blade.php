@php($editing = $admin->exists)
@php($selectedPermissionIds = old('permissions', $admin->permissions?->pluck('id')->all() ?? []))

<x-layouts.admin active="admins" :title="$editing ? 'Modifier l\'administrateur' : 'Nouvel administrateur'">
    <div class="max-w-2xl bg-surface-container-lowest rounded-xl shadow-[0_4px_20px_rgba(0,0,0,0.03)] p-8">
        <form
            method="POST"
            action="{{ $editing ? route('admin.admins.update', $admin) : route('admin.admins.store') }}"
            class="flex flex-col gap-6"
            x-data="{ superAdmin: {{ old('is_super_admin', $admin->is_super_admin ?? false) ? 'true' : 'false' }} }"
        >
            @csrf
            @if($editing) @method('PUT') @endif

            <div>
                <label class="font-label-sm text-label-sm text-on-surface-variant">Nom complet</label>
                <input type="text" name="name" value="{{ old('name', $admin->name) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" required />
                @error('name') <p class="text-error font-label-sm text-label-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Email</label>
                    <input type="email" name="email" value="{{ old('email', $admin->email) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" required />
                    @error('email') <p class="text-error font-label-sm text-label-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="font-label-sm text-label-sm text-on-surface-variant">Téléphone</label>
                    <input type="text" name="phone" value="{{ old('phone', $admin->phone) }}" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" />
                </div>
            </div>

            <div>
                <label class="font-label-sm text-label-sm text-on-surface-variant">{{ $editing ? 'Nouveau mot de passe (laisser vide pour ne pas changer)' : 'Mot de passe' }}</label>
                <input type="password" name="password" class="w-full bg-surface border-b border-outline-variant py-2 focus:border-primary focus:ring-0 font-body-md text-body-md" {{ $editing ? '' : 'required' }} />
                @error('password') <p class="text-error font-label-sm text-label-sm mt-1">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-start gap-3 p-4 rounded-lg bg-surface-container-low cursor-pointer">
                <input type="checkbox" name="is_super_admin" value="1" x-model="superAdmin" class="mt-1 rounded-sm" />
                <span>
                    <span class="block font-body-md text-body-md font-semibold text-on-surface">Super administrateur</span>
                    <span class="block font-label-sm text-label-sm text-on-surface-variant">Accès total à toutes les sections, y compris la gestion des autres administrateurs. À réserver aux personnes de confiance.</span>
                </span>
            </label>

            <div x-show="!superAdmin" x-cloak>
                <p class="font-label-sm text-label-sm text-on-surface-variant mb-3">Sections accessibles à cet administrateur :</p>
                <div class="flex flex-col gap-4">
                    @foreach($permissions as $group => $groupPermissions)
                        <div>
                            <h4 class="font-label-sm text-label-sm uppercase text-on-surface mb-2">{{ $group }}</h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach($groupPermissions as $permission)
                                    <label class="flex items-center gap-2 px-3 py-2 rounded-lg border border-outline-variant cursor-pointer has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked(in_array($permission->id, $selectedPermissionIds)) class="rounded-sm" />
                                        <span class="font-body-md text-body-md">{{ $permission->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-primary text-on-primary px-8 py-3 rounded-full font-label-sm text-label-sm uppercase tracking-widest">Enregistrer</button>
                <a href="{{ route('admin.admins.index') }}" class="px-8 py-3 rounded-full border border-outline text-on-surface-variant font-label-sm text-label-sm uppercase tracking-widest">Annuler</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
