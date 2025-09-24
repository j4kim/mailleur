<?php

namespace App\Filament\Resources\Campaigns\RelationManagers;

use App\Enums\RecipientStatus;
use App\Filament\Actions\Recipient as Actions;
use App\Models\Campaign;
use App\Models\Recipient;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Livewire\Component;
use Throwable;

use function App\Tools\errorNotif;
use function App\Tools\successNotif;

class RecipientsRelationManager extends RelationManager
{
    protected static string $relationship = 'recipients';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        /** @var Campaign $campaign */
        $campaign = $this->getOwnerRecord();

        return $schema
            ->components([
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                ...collect($campaign->columns)->map(function (string $name) {
                    return Textarea::make("data.$name")->label($name)->rows(1);
                }),
            ]);
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
                Action::make('import recipients')
                    ->schema([
                        FileUpload::make('csv_file')
                            ->label("CSV file")
                            ->required()
                            ->acceptedFileTypes(['text/csv']),
                    ])->action(function (array $data, Action $action, Component $livewire) use ($campaign) {
                        try {
                            $campaign->importCsv($data['csv_file']);
                            successNotif('Recipients imported successfully');
                            return $livewire->redirect(request()->header('Referer'));
                        } catch (Throwable $e) {
                            errorNotif($e->getMessage());
                            $action->cancel();
                        }
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()->label("Edit data"),
                    Actions\Generate::make(),
                    Actions\Write::make(),
                    Actions\Ready::make(),
                    Actions\SetStatus::make(),
                    Actions\Preview::make(),
                    Actions\Send::make(),
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
