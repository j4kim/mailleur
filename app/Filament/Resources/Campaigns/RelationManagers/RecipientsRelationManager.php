<?php

namespace App\Filament\Resources\Campaigns\RelationManagers;

use App\Enums\RecipientStatus;
use App\Filament\Actions\Bulk\GenerateRecipientsBulkAction;
use App\Filament\Actions\Bulk\SendRecipientsBulkAction;
use App\Filament\Actions\Bulk\SetStatusRecipientsBulkAction;
use App\Filament\Actions\GenerateAction;
use App\Filament\Actions\PreviewRecipientAction;
use App\Filament\Actions\ReadyRecipientAction;
use App\Filament\Actions\SendRecipientAction;
use App\Filament\Actions\SetStatusRecipientAction;
use App\Filament\Actions\WriteRecipientAction;
use App\Models\Campaign;
use App\Models\Recipient;
use Exception;
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
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;
use Throwable;

use function App\Tools\errorNotif;
use function App\Tools\formatAddress;
use function App\Tools\notif;
use function App\Tools\prose;
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
                GenerateAction::make('generate')
                    ->visible(fn(Recipient $r) => $r->status == RecipientStatus::Initial),
                WriteRecipientAction::make('write'),
                PreviewRecipientAction::make('preview'),
                ActionGroup::make([
                    EditAction::make()->label("Edit data"),
                    GenerateAction::make('regenerate')
                        ->label("Regenerate")
                        ->visible(fn(Recipient $r) => $r->status == RecipientStatus::Customized),
                    ReadyRecipientAction::make('ready'),
                    SetStatusRecipientAction::make('status'),
                    SendRecipientAction::make('send'),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    GenerateRecipientsBulkAction::make('generate'),
                    SetStatusRecipientsBulkAction::make('status'),
                    SendRecipientsBulkAction::make('send'),
                ]),
            ]);
    }
}
