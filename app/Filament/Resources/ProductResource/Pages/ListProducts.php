<?php

namespace App\Filament\Resources\ProductResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'todos' => Tab::make(),
            'products' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 1)),
            'services' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 3)),
        ];
    }
}