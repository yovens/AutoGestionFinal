@props(['name', 'value' => null, 'label' => 'Carburant', 'idPrefix' => 'fuel'])
@php $current = old($name, $value); @endphp
<fieldset class="field" style="border:0;padding:0;margin-inline:0;margin-bottom:0">
    <legend class="field__label" style="margin-bottom:6px">{{ $label }}</legend>
    <div class="fuel">
        @foreach (\App\Models\LoanCart::FUEL as $lvl => $text)
            <input type="radio" name="{{ $name }}" id="{{ $idPrefix }}-{{ $lvl }}" value="{{ $lvl }}" @checked((string) $current === (string) $lvl) required>
            <label for="{{ $idPrefix }}-{{ $lvl }}"><i style="--lvl: {{ $lvl * 25 }}%"></i>{{ $text }}</label>
        @endforeach
    </div>
    @error($name)<span class="field__error">{{ $message }}</span>@enderror
</fieldset>
