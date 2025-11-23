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
                $contentType = $response->header('Content-Type');
                
                // Check if response is an image or treat as binary if not JSON
                if (str_contains($contentType, 'image') || !is_array($response->json())) {
                    $base64Image = base64_encode($response->body());
                    $mimeType = $contentType ?? 'image/png'; // Default to png if unknown
                    $this->qr_code = "data:{$mimeType};base64,{$base64Image}";
                    
                    Notification::make()
                        ->success()
                        ->title('QR Code recebido')
                        ->send();

                    $this->dispatch('open-modal', id: 'qr-code-modal');
                } else {
                    // Fallback for JSON response (just in case)
                    $body = $response->json();
                    $base64 = null;

                    if (isset($body[0]['data']['base64'])) {
                        $base64 = $body[0]['data']['base64'];
                    } elseif (isset($body['data']['base64'])) {
                        $base64 = $body['data']['base64'];
                    } elseif (isset($body['base64'])) {
                        $base64 = $body['base64'];
                    }

                    if ($base64) {
                        $this->qr_code = $base64;
                        Notification::make()->success()->title('QR Code gerado')->send();
                        $this->dispatch('open-modal', id: 'qr-code-modal');
                    } else {
                        Notification::make()
                            ->warning()
                            ->title('Resposta inesperada')
                            ->body('Não foi possível identificar o QR Code na resposta.')
                            ->send();
                    }
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
    public function checkConnectionStatus(): void
    {
        $url = 'https://webhook.fredericomoura.com.br/webhook/checa-status';
        $instanceName = $this->data['instance_name'] ?? null;

        try {
            $response = Http::post($url, [
                'instance_name' => $instanceName,
                'timestamp' => now()->toIso8601String(),
            ]);

            if ($response->successful()) {
                Notification::make()
                    ->success()
                    ->title('Status da conexão verificado')
                    ->body('O sistema verificou o status da conexão.')
                    ->send();
                
                // Optional: Close modal if connected
                // $this->dispatch('close-modal', id: 'qr-code-modal');
            } else {
                Notification::make()
                    ->warning()
                    ->title('Verificação de status')
                    ->body('Não foi possível confirmar a conexão no momento.')
                    ->send();
            }
        } catch (\Exception $e) {
            // Silent fail or log
        }
    }
}
