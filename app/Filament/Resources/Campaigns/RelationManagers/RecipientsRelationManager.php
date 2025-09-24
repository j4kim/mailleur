<?php

namespace App\Filament\Resources\Campaigns\RelationManagers;

use App\Enums\RecipientStatus;
use App\Filament\Actions\Campaign\ImportRecipients;
use App\Filament\Actions\Recipient as Actions;
use App\Models\Campaign;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RecipientsRelationManager extends RelationManager
{
    protected static string $relationship = 'recipients';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        /** @var Campaign $campaign */
        $campaign = $this->getOwnerRecord();

        $columns = [
            TextColumn::make('created_at')
                ->dateTime('d.m.Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')
                ->dateTime('d.m.Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('email')
                ->label('Email')
                ->sortable()
                ->searchable(),
            ...collect($campaign->columns)->map(function (string $name) {
                return TextColumn::make("data.$name")
                    ->label($name)
                    ->sortable()
                    ->searchable()
                    ->toggleable();
            }),
            TextColumn::make('status')
                ->badge(),
            TextColumn::make('sent_at')
                ->dateTime('d.m.Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('failed_at')
                ->dateTime('d.m.Y H:i')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ];

        return $table
            ->recordTitleAttribute('email')
            ->columns($columns)
            ->filters([
                SelectFilter::make('status')->options(RecipientStatus::class)
            ])
            ->headerActions([
                CreateAction::make()->outlined(),
                ImportRecipients::make()->init($campaign),
            ])
            ->recordActions([
                Actions\Generate::make()->for(RecipientStatus::Initial),
                Actions\Ready::make()->for(RecipientStatus::Customized),
                Actions\Send::make()->for(RecipientStatus::Ready),
                Actions\Preview::make()->for(RecipientStatus::Sent),
                ActionGroup::make([
                    Actions\EditData::make(),
                    Actions\SetStatus::make(),
                    Actions\Generate::make(),
                    Actions\Ready::make(),
                    Actions\Write::make(),
                    Actions\Send::make(),
                    Actions\Preview::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    Actions\Bulk\Generate::make(),
                    Actions\Bulk\SetStatus::make(),
                    Actions\Bulk\Send::make(),
                ]),
            ]);
    }
}
