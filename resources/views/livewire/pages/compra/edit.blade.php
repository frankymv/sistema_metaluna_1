<x-frk.components.template-crud>
    <x-slot:title>
        <x-frk.components.title label="Editar {{$title}}" />
    </x-slot>
    <x-slot:body>
        @include('livewire.pages.compra.form')

    </x-slot>
    <x-slot:footer>
        <x-frk.components.button label="editar" wire:click="update({{$id_data}})" />
        <x-frk.components.button label="cancelar" wire:click="cancel()" />
    </x-slot>
</x-frk.modal>

