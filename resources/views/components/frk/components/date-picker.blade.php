@props(['label'=>'','placeholder'=>'Seleccione aqui','erase'=>'true'])
<div class="w-full flex-wrap items-center  px-1 "
    x-data
    x-init="flatpickr($refs.datewidget, {wrap: true, enableTime: false, dateFormat: 'Y-m-d'});"
    x-ref="datewidget">
            <x-frk.components.label label="{{$label}}" class="font-semibold" />

        <div class="flex w-full align-middle align-content-center">


            <x-frk.components.input
            x-ref="datetime"
                id="datetime"
                data-input
            {{$attributes}}
            placeholder="{{$placeholder}}" />

            @if ($erase===true)


            <a title="clear" data-clear>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w- mt-1 ml-1" viewBox="0 0 20 20" fill="#c53030">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd"/>
                </svg>
            </a>
            @endif

        </div>
        @include('components.frk.components.error')
</div>
