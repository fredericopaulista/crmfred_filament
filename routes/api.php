
use App\Http\Controllers\WhatsAppController;

Route::get('/whatsapp/webhook', [WhatsAppController::class, 'webhook']);
Route::post('/whatsapp/webhook', [WhatsAppController::class, 'webhook']);
