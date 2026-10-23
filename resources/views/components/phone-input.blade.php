@props(['name', 'countryName', 'id' => null, 'value' => null, 'country' => null, 'required' => true, 'invalid' => false])

@php
    // An existing E.164 number is split back into its country and local part.
    $e164 = $value && str_starts_with($value, '+') ? $value : null;
    $selectedCountry = strtoupper((string) old($countryName, $country ?? ($e164 ? \App\Support\Phone::countryOf($e164) : null) ?? config('marketplace.default_country')));
    $number = old($name, $e164 ? \App\Support\Phone::national($e164) : $value);
    $id ??= $name;
@endphp

<div class="flex items-stretch rounded-xl border border-outline-variant bg-surface-container-lowest focus-within:border-primary focus-within:ring-3 focus-within:ring-primary/15 {{ $invalid ? '!border-error' : '' }}">
    <label for="{{ $id }}-country" class="sr-only">Indicatif du pays</label>
    <select id="{{ $id }}-country" name="{{ $countryName }}" class="w-[104px] shrink-0 bg-transparent border-0 border-r border-outline-variant rounded-l-xl pl-3 pr-1 text-[14px] font-semibold text-on-surface-variant focus:outline-none focus:ring-0 cursor-pointer">
        @foreach(config('countries') as $code => [$countryLabel, $dial])
            <option value="{{ $code }}" @selected($selectedCountry === $code)>{{ $code }} +{{ $dial }}</option>
        @endforeach
    </select>
    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="tel"
        inputmode="tel"
        autocomplete="tel-national"
        value="{{ $number }}"
        placeholder="Numéro de téléphone"
        @if($required) required data-message="Indiquez un numéro de téléphone." @endif
        class="flex-1 min-w-0 bg-transparent border-0 px-3 py-2.5 text-[15px] focus:outline-none focus:ring-0"
    />
</div>
