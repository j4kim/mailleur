<?php

namespace App\Filament\Resources\Campaigns\RelationManagers;

use App\Enums\RecipientStatus;
use App\Models\Campaign;
use App\Models\Recipient;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
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
        ];

        return $table
            ->recordTitleAttribute('email')
            ->columns($columns)
            ->filters([
                //
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
                            $this->successNotif('Recipients imported successfully');
                            return $livewire->redirect(request()->header('Referer'));
                        } catch (Throwable $e) {
                            $this->errorNotif($e->getMessage());
                            $action->cancel();
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make('generate')
                    ->label(
                        fn(Recipient $recipient) => $recipient->mail_body ? "Regenerate" : "Generate"
                    )
                    ->hidden(fn() => !$campaign->template)
                    ->mutateRecordDataUsing(
                        fn(Recipient $recipient): array =>
                        ['mail_body' => $recipient->generateMailBody()]
                    )
                    ->mutateDataUsing(function (array $data): array {
                        $data['status'] = RecipientStatus::Customized;
                        return $data;
                    })
                    ->schema([
                        RichEditor::make('mail_body')
                    ])
                    ->slideOver(),
                EditAction::make('write')
                    ->label("Write")
                    ->hidden(fn(Recipient $recipient) => !$recipient->mail_body)
                    ->schema([
                        RichEditor::make('mail_body')
                    ])
                    ->slideOver(),
                ActionGroup::make([
                    EditAction::make()->label("Edit data"),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('generate')
                        ->label("Generate / Regenerate selected")
                        ->color('primary')
                        ->icon('heroicon-m-pencil-square')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->generateAndSave();
                        }),
                ]),
            ]);
    }

    public function successNotif(string $message)
    {
        Notification::make()
            ->title('Success')
            ->body($message)
            ->success()
            ->send();
    }

    public function errorNotif(string $message)
    {
        Notification::make()
            ->title('Error')
            ->body($message)
            ->status('danger')
            ->send();
    }
}
