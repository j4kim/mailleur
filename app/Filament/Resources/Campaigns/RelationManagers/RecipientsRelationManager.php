<?php

namespace App\Filament\Resources\Campaigns\RelationManagers;

use App\Enums\RecipientStatus as RS;
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
            TextColumn::make('id')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
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
            TextColumn::make('clicks_count')
                ->visible($campaign->enable_logged_links)
                ->counts('clicks')
                ->label('Clicks')
                ->sortable()
                ->toggleable(),
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
                SelectFilter::make('status')->options(RS::class)
            ])
            ->headerActions([
                CreateAction::make()
                    ->outlined()
                    ->schema(Actions\EditData::getCustomSchema($campaign)),
                ImportRecipients::make()->init($campaign),
            ])
            ->recordActions([
                Actions\Generate::make('generate')->for(RS::Initial),
                Actions\Ready::make('ready')->for(RS::Customized),
                Actions\Send::make('send')->for(RS::Ready),
                Actions\CancelSchedule::make()->for(RS::Scheduled),
                Actions\Preview::make('preview')->for(RS::Sent),
                Actions\Logs::make('logs')->for(RS::Failed),
                ActionGroup::make([
                    Actions\EditData::make()->for(RS::Initial, RS::Customized, RS::Failed),
                    Actions\SetStatus::make()->notFor(RS::Scheduled),
                    Actions\Generate::make()->for(RS::Customized, RS::Failed),
                    Actions\Ready::make()->for(RS::Failed),
                    Actions\Write::make()->for(RS::Customized, RS::Failed),
                    Actions\Send::make()->for(RS::Failed),
                    Actions\Schedule::make()->for(RS::Initial, RS::Customized, RS::Ready, RS::Failed),
                    Actions\Preview::make()->for(RS::Customized, RS::Ready, RS::Failed),
                    Actions\Logs::make()->for(RS::Initial, RS::Customized, RS::Ready, RS::Sent),
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
            ])
            ->paginated([25, 50, 100, 'all'])
            ->persistSortInSession();
    }
}
