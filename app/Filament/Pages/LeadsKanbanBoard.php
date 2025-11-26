<?php

namespace App\Filament\Pages;

use App\Models\Lead;
use Mokhosh\FilamentKanban\Pages\KanbanBoard;

class LeadsKanbanBoard extends KanbanBoard
{
    protected static string $model = Lead::class;
    protected static ?string $title = 'Pipeline de Vendas';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Vendas';
    protected static ?int $navigationSort = 2;

    protected function statuses(): \Illuminate\Support\Collection
    {
        return collect([
            [
                'id' => 'new',
                'title' => 'Novo',
            ],
            [
                'id' => 'contacted',
                'title' => 'Contatado',
            ],
            [
                'id' => 'qualified',
                'title' => 'Qualificado',
            ],
            [
                'id' => 'negotiation',
                'title' => 'Em Negociação',
            ],
            [
                'id' => 'won',
                'title' => 'Ganho',
            ],
            [
                'id' => 'lost',
                'title' => 'Perdido',
            ],
        ]);
    }

    protected function records(): \Illuminate\Support\Collection
    {
        return Lead::all();
    }

    public function onStatusChanged(int|string $recordId, string $status, array $fromOrderedIds, array $toOrderedIds): void
    {
        Lead::find($recordId)->update(['status' => $status]);
    }

    public function onSortChanged(int|string $recordId, string $status, array $orderedIds): void
    {
        // Optional: Implement sorting logic if needed
    }

    protected function getEditModalFormSchema(null|int|string $recordId): array
    {
        return [
            // Optional: Add form fields for editing the record in a modal
        ];
    }

    protected function getEditModalRecordData(null|int|string $recordId): array
    {
        return Lead::find($recordId)->toArray();
    }

    protected function editRecord(int|string $recordId, array $data, array $state): void
    {
        Lead::find($recordId)->update($data);
    }
    
    // Customize the card content
    protected function recordTitle(mixed $record): string
    {
        return $record->name;
    }

    protected function recordContent(mixed $record): string
    {
        return $record->phone ?? 'Sem telefone';
    }
}
