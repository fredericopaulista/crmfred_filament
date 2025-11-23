<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Http;

class WhatsAppSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Configurações';
    protected static ?string $navigationLabel = 'WhatsApp Connection';
    protected static ?string $title = 'WhatsApp Configuration';

    protected static string $view = 'filament.pages.whats-app-settings';

    public ?string $qr_code = null;

    public ?array $data = [];

    public function mount(): void
    {
        $defaultInstanceName = auth()->user()->name . '_' . rand(0, 10);

        $this->form->fill([
            'n8n_webhook_url' => Setting::where('key', 'n8n_webhook_url')->value('value') ?? 'https://n8n.fredericomoura.com.br/webhook-test/cria-instancia',
            'phone_number' => Setting::where('key', 'phone_number')->value('value'),
            'instance_name' => Setting::where('key', 'instance_name')->value('value') ?? $defaultInstanceName,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('n8n_webhook_url')
                    ->label('n8n Webhook URL')
                    ->required()
                    ->url()
                    ->columnSpanFull(),
                Hidden::make('instance_name')
                    ->required(),
                TextInput::make('phone_number')
                    ->label('Número do WhatsApp')
                    ->placeholder('+5531999999999')
                    ->helperText('Formato: +5531999999999')
                    ->required()
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::updateOrCreate(
            ['key' => 'n8n_webhook_url'],
            ['value' => $data['n8n_webhook_url']]
        );

        Setting::updateOrCreate(
            ['key' => 'phone_number'],
            ['value' => $data['phone_number']]
        );

        Setting::updateOrCreate(
            ['key' => 'instance_name'],
            ['value' => $data['instance_name']]
        );

        Notification::make()
            ->success()
            ->title('Settings saved successfully')
            ->send();
    }

    public function connect(): void
    {
        $this->save();
        $url = $this->data['n8n_webhook_url'];
        $phone = $this->data['phone_number'];
        $instanceName = $this->data['instance_name'];

        try {
            $response = Http::post($url, [
                'action' => 'create_instance',
                'phone_number' => $phone,
                'instance_name' => $instanceName,
                'timestamp' => now()->toIso8601String(),
            ]);

            if ($response->successful()) {
                $body = $response->json();
                
                // Handle array response
                if (is_array($body) && isset($body[0]['data']['base64'])) {
                    $this->qr_code = $body[0]['data']['base64'];
                    
                    Notification::make()
                        ->success()
                        ->title('QR Code gerado com sucesso')
                        ->body('Escaneie o QR Code para conectar.')
                        ->send();
                } else {
                    Notification::make()
                        ->warning()
                        ->title('Conexão iniciada')
                        ->body('Verifique se o QR Code foi gerado corretamente.')
                        ->send();
                }
            } else {
                Notification::make()
                    ->danger()
                    ->title('Falha na conexão')
                    ->body('Erro: ' . $response->status())
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Erro de conexão')
                ->body($e->getMessage())
                ->send();
        }
    }
}
