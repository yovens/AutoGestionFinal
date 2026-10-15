{{-- Chan fòm machin (kreyasyon + modifikasyon). $vehicle ka null. --}}
@php $vehicle = $vehicle ?? null; @endphp
<div class="form-grid">
    <x-field name="brand" label="Marque" :value="$vehicle?->brand" placeholder="Toyota" required />
    <x-field name="model" label="Modèle" :value="$vehicle?->model" placeholder="Hilux" required />
    <x-field name="plate" label="Plaque" :value="$vehicle?->plate" placeholder="AA-12345" required style="text-transform:uppercase" />
    <x-field name="year" type="number" label="Année" :value="$vehicle?->year ?? date('Y')" min="1950" max="{{ date('Y') + 1 }}" required />
    <x-field name="price" type="number" label="Prix de vente ($)" :value="$vehicle?->price" min="0" step="100" required />
    <x-field name="loan_price" type="number" label="Location par jour ($)" :value="$vehicle?->loan_price" min="0" required />
    <x-field name="quantity" type="number" label="Exemplaires en stock" :value="$vehicle?->quantity ?? 1" min="0" max="999" required
             hint="À 0, le véhicule n’apparaît plus dans le catalogue." />
    <x-field name="image" type="file" label="Photo" accept="image/jpeg,image/png,image/webp" :required="!$vehicle"
             hint="{{ $vehicle ? 'Laissez vide pour garder la photo actuelle.' : 'JPG, PNG ou WebP, 4 Mo max.' }}" />
</div>
