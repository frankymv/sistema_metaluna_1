<div>
  <x-modal name="texto" show="true" >
        <div class="flex w-full flex-wrap m-4">

          <x-forms.label-title label="Detalle {{$title}}" />

          <div class="flex flex-wrap">

              <x-forms.label-input disabled label="nombre"  wire:model="nombre" />

              <x-forms.label-input disabled label="descripcion" wire:model="descripcion" />

              <x-forms.label-input disabled label="created_at" wire:model="created_at" />

              <x-forms.label-input disabled label="updated_at" wire:model="updated_at" />

            <div  class="flex w-full md:w-1/2 justify-center content-center" x-data="{value: @entangle('estado')}">
                <div disabled class="flex items-center m-2 cursor-pointer cm-toggle-wrapper">
                    <span class="font-semibold text-xs mr-1">
                      Inactivo
                    </span>

                    <div class="rounded-full w-8 h-4 p-0.5" :class="{'bg-red-500': value == 0,'bg-green-500': value == 1}">
                        <div class="rounded-full w-3 h-3 bg-white transform mx-auto duration-300 ease-in-out" :class="{'-translate-x-2': value == 0,'translate-x-2': value == 1}"></div>
                    </div>
                    <span class="font-semibold text-xs ml-1">
                        Activo
                    </span>
              </div>
            </div>

            <div class="flex w-full md:w-1/2">



              <x-buttons.cancel-button wire:click.prevent="cancel()">

                Cancelar
              </x-buttons.save-button>

            </div>
        </div>
  </x-modal>
</div>



<!--

<form>
    <div class="form-group">
        <input type="hidden" wire:model="post_id">
        <label for="exampleFormControlInput1">Title</label>
        <input type="text" class="form-control" wire:model="title" id="exampleFormControlInput1" placeholder="Enter Title">
        @error('title') <span class="text-danger">{{ $message }}</span>@enderror
    </div>
    <div class="form-group">
        <label for="exampleFormControlInput2">Description</label>
        <input type="text" class="form-control" wire:model="description" id="exampleFormControlInput2" placeholder="Enter Description">
        @error('description') <span class="text-danger">{{ $message }}</span>@enderror
    </div>
    <button wire:click.prevent="update()" class="btn btn-dark">Update</button>
    <button wire:click.prevent="cancel()" class="btn btn-danger">Cancel</button>
</form>

->