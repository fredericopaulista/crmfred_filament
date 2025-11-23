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

        @if($qr_code)
            <div class="mt-6 p-4 border border-gray-200 rounded-lg bg-white dark:bg-gray-900 dark:border-gray-700 flex flex-col items-center">
                <h3 class="text-lg font-medium mb-4">Escaneie o QR Code</h3>
                <img src="{{ $qr_code }}" alt="WhatsApp QR Code" class="max-w-xs border-4 border-white shadow-lg rounded-lg" />
                <p class="mt-4 text-sm text-gray-500">Abra o WhatsApp no seu celular > Menu > Aparelhos conectados > Conectar um aparelho</p>
            </div>
        @endif
    </x-filament-panels::form>
</x-filament-panels::page>
