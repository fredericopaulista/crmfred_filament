<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <div class="flex gap-3 mt-6">
            <x-filament::button type="submit">
                Save Settings
            </x-filament::button>

            <x-filament::button 
                type="button" 
                color="success" 
                wire:click="connect"
                wire:loading.attr="disabled"
            >
                Conectar
            </x-filament::button>
        </div>
    </x-filament-panels::form>
</x-filament-panels::page>
