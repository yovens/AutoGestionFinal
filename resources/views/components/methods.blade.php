@props(['name' => 'method', 'value' => null, 'label' => 'Mode de paiement', 'idPrefix' => 'm'])
{{-- Chwa mòd peman an bouton (yon sèl klik nan comptoir la). --}}
@php $current = old($name, $value); @endphp
<fieldset class="field" style="border:0;padding:0;margin:0">
    <legend class="field__label" style="margin-bottom:6px">{{ $label }}</legend>
    <div class="methods">
        @foreach (\App\Models\Transaction::METHODS as $key => $text)
            <input type="radio" name="{{ $name }}" id="{{ $idPrefix }}-{{ $key }}" value="{{ $key }}" @checked($current === $key) {{ $attributes }}>
            <label for="{{ $idPrefix }}-{{ $key }}">{{ $text }}</label>
        @endforeach
    </div>
    @error($name)<span class="field__error">{{ $message }}</span>@enderror
</fieldset>
