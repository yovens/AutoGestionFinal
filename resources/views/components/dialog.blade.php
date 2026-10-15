@props(['id', 'title', 'size' => null, 'openOnLoad' => false])
<dialog id="{{ $id }}" {{ $attributes->class(['modal', 'modal--' . $size => $size]) }} aria-labelledby="{{ $id }}-title" @if ($openOnLoad) data-open-on-load @endif>
    <div class="modal__head">
        <h2 class="h3" id="{{ $id }}-title">{{ $title }}</h2>
        <button type="button" class="btn btn--ghost btn--icon btn--sm" data-dialog-close aria-label="Fermer"><x-icon name="x" /></button>
    </div>
    {{ $slot }}
</dialog>
