@props(['model'])
{{-- Badj estati pou LoanCart oswa Transaction (tou de gen status_label + status_tone). --}}
<x-badge :tone="$model->status_tone" {{ $attributes }}>{{ $model->status_label }}</x-badge>
