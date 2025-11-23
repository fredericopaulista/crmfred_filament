<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
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
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'WhatsApp Connection';
    protected static ?string $title = 'WhatsApp Configuration';

    protected static string $view = 'filament.pages.whats-app-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'n8n_webhook_url' => Setting::where('key', 'n8n_webhook_url')->value('value') ?? 'https://n8n.fredericomoura.com.br/webhook-test/cria-instancia',
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

        Notification::make()
            ->success()
            ->title('Settings saved successfully')
            ->send();
    }

    public function connect(): void
    {
        $this->save();
        $url = $this->data['n8n_webhook_url'];

        try {
            $response = Http::post($url, [
                'action' => 'create_instance',
                'timestamp' => now()->toIso8601String(),
            ]);

            if ($response->successful()) {
                Notification::make()
                    ->success()
                    ->title('Connection request sent successfully')
                    ->body('n8n responded with: ' . $response->body())
                    ->send();
            } else {
                Notification::make()
                    ->danger()
                    ->title('Connection failed')
                    ->body('n8n responded with error: ' . $response->status())
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Connection error')
                ->body($e->getMessage())
                ->send();
        }
    }
}
