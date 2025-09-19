<?php

namespace App\Filament\Resources\Campaigns\RelationManagers;

use App\Models\Campaign;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Livewire\Component;
use Throwable;

class RecipientsRelationManager extends RelationManager
{
    protected static string $relationship = 'recipients';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                KeyValue::make('data')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        /** @var Campaign $campaign */
        $campaign = $this->getOwnerRecord();

        $columns = [
            TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')
                ->dateTime()
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
        ];

        return $table
            ->recordTitleAttribute('email')
            ->columns($columns)
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                Action::make('import recipients')
                    ->schema([
                        FileUpload::make('csv_file')->label("CSV file")->required(),
                    ])->action(function (array $data, Action $action, Component $livewire) use ($campaign) {
                        try {
                            $campaign->importCsv($data['csv_file']);

                            Notification::make()
                                ->title('Success')
                                ->body('Recipients imported successfully')
                                ->success()
                                ->send();

                            return $livewire->redirect(request()->header('Referer'));
                        } catch (Throwable $e) {
                            Notification::make()
                                ->title('Error')
                                ->body($e->getMessage())
                                ->status('danger')
                                ->send();
                            $action->cancel();
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
