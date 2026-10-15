@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'hint' => null,
    'options' => null,      // pou <select>: [valè => etikèt]
    'placeholder' => null,
])
@php
    $id = $attributes->get('id', 'f-' . str_replace(['[', ']', '.'], '-', $name) . '-' . substr(md5(uniqid('', true)), 0, 5));
    $error = $errors->first($name);
    $current = $type === 'password' || $type === 'file' ? null : old($name, $value);
@endphp
<div {{ $attributes->only('class')->class(['field']) }}>
    @if ($label)
        <label class="field__label" for="{{ $id }}">{{ $label }}</label>
    @endif

    @if ($type === 'select')
        <select id="{{ $id }}" name="{{ $name }}" class="select" @if ($error) aria-invalid="true" @endif {{ $attributes->except(['class', 'id']) }}>
            @if ($placeholder)<option value="">{{ $placeholder }}</option>@endif
            @foreach ($options ?? [] as $val => $text)
                <option value="{{ $val }}" @selected((string) $current === (string) $val)>{{ $text }}</option>
            @endforeach
        </select>
    @elseif ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" class="textarea" placeholder="{{ $placeholder }}" @if ($error) aria-invalid="true" @endif {{ $attributes->except(['class', 'id']) }}>{{ $current }}</textarea>
    @else
        <input id="{{ $id }}" type="{{ $type }}" name="{{ $name }}" class="input" value="{{ $current }}" placeholder="{{ $placeholder }}"
               @if ($error) aria-invalid="true" aria-describedby="{{ $id }}-err" @endif {{ $attributes->except(['class', 'id']) }}>
    @endif

    @if ($error)
        <span class="field__error" id="{{ $id }}-err">{{ $error }}</span>
    @elseif ($hint)
        <span class="field__hint">{{ $hint }}</span>
    @endif
</div>
