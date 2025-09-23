<?php

namespace App\Filament\Resources\Campaigns\RelationManagers;

use App\Enums\RecipientStatus;
use App\Filament\Actions\GenerateAction;
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
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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
                            $this->successNotif('Recipients imported successfully');
                            return $livewire->redirect(request()->header('Referer'));
                        } catch (Throwable $e) {
                            $this->errorNotif($e->getMessage());
                            $action->cancel();
                        }
                    }),
            ])
            ->recordActions([
                GenerateAction::make('generate')
                    ->visible(fn(Recipient $r) => $r->status == RecipientStatus::Initial),
                EditAction::make('write')
                    ->label("Write")
                    ->visible(fn(Recipient $r) => in_array($r->status, [
                        RecipientStatus::Customized,
                        RecipientStatus::Ready
                    ]))
                    ->schema([RichEditor::make('mail_body')])
                    ->slideOver(),
                ActionGroup::make([
                    EditAction::make()->label("Edit data"),
                    GenerateAction::make('regenerate')
                        ->label("Regenerate")
                        ->visible(fn(Recipient $r) => $r->status == RecipientStatus::Customized),
                    Action::make('ready')
                        ->label("Mark as Ready")
                        ->visible(fn(Recipient $r) => $r->status === RecipientStatus::Customized)
                        ->color('primary')
                        ->icon(Heroicon::Check)
                        ->action(function (Recipient $r) {
                            $r->status = RecipientStatus::Ready;
                            $r->save();
                        }),
                    EditAction::make('status')
                        ->label("Set status")
                        ->icon(Heroicon::Tag)
                        ->schema([
                            ToggleButtons::make('status')->options(RecipientStatus::class)
                        ]),
                    Action::make('send')
                        ->icon(Heroicon::PaperAirplane)
                        ->action(fn(Recipient $r) => $r->sendOne())
                        ->visible(fn(Recipient $r) => in_array($r->status, [
                            RecipientStatus::Customized,
                            RecipientStatus::Ready
                        ]))
                        ->color('success'),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('generate')
                        ->label("Generate / Regenerate selected")
                        ->color('primary')
                        ->icon(Heroicon::Bolt)
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each(fn(Recipient $r) => $r->generateAndSave());
                        }),
                    BulkAction::make('status')
                        ->label("Set status for selected")
                        ->color('primary')
                        ->icon(Heroicon::Tag)
                        ->schema([
                            ToggleButtons::make('status')->options(RecipientStatus::class)
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(fn(Recipient $r) => $r->update($data));
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
