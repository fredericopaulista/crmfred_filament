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

        <x-filament::modal id="qr-code-modal" width="md">
            <x-slot name="heading">
                Escaneie o QR Code
            </x-slot>

            <div 
                class="flex flex-col items-center justify-center p-4"
                x-data
                x-init="
                    if (@js($qr_code)) {
                        setTimeout(() => {
                            $wire.checkConnectionStatus();
                        }, 15000);
                    }
                "
            >
                @if($qr_code)
                    <img src="{{ $qr_code }}" alt="WhatsApp QR Code" class="max-w-full border-4 border-white shadow-lg rounded-lg" />
                    <p class="mt-4 text-sm text-center text-gray-500">
                        Abra o WhatsApp no seu celular <br>
                        <strong>Menu > Aparelhos conectados > Conectar um aparelho</strong>
                    </p>
                    <p class="mt-2 text-xs text-gray-400">Verificando conexão em 15 segundos...</p>
                @else
                    <div class="flex items-center justify-center h-48 w-full bg-gray-100 rounded-lg">
                        <x-filament::loading-indicator class="h-10 w-10" />
                    </div>
                @endif
            </div>
        </x-filament::modal>
    </x-filament-panels::form>
</x-filament-panels::page>
