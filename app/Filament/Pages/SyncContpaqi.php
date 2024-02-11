<?php

namespace App\Filament\Pages;

use App\Models\Client;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\admClientes;
use Filament\Forms\Components;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;

class SyncContpaqi extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.sync-contpaqi';

    protected static ?string $navigationGroup = 'CONTPAQi';

    protected ?string $subheading = 'Esta sección es para sincronizar la información con la empresa contpaqi';

    public ?int $accept = 0;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Components\Checkbox::make('accept')
                    ->label('Acepto comenzar la sincronización de los clientes. Este proceso puede demorar de acuerdo a la cantidad de clientes.')
                    ->required()
            ]);
    }

    public function submit()
    {
        try {
            DB::connection('sqlcompac')->getPdo();
        } catch (\Throwable $th) {
            Notification::make()
            ->title('Error en la conexion con CONTPAQi')
            ->danger()
            ->send();

            return false;
        }

        $clients = DB::connection('sqlcompac')
        ->table('admClientes')
        ->where([
            ['CTIPOCLIENTE', 1], 
            ['CESTATUS', 1], 
            ])
        ->get();

        foreach ($clients as $client) {
            Client::updateOrCreate([
                'code' => $client->CCODIGOCLIENTE
            ], [
                'name' => $client->CRAZONSOCIAL,
                'rfc' => $client->CRFC,
                'tradename' => $client->CDENCOMERCIAL,
                'email1' => $client->CEMAIL1,
                'email2' => $client->CEMAIL2,
                'email3' => $client->CEMAIL3,
            ]);
        }

        Notification::make()
            ->title('Clientes sincronizados')
            ->success()
            ->send();

        return redirect()->route('filament.admin.pages.dashboard');
    }

}