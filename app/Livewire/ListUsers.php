<?php

namespace App\Livewire;

use App\Models\Team;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Livewire\Component;
use Throwable;

class ListUsers extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        /** @var Team $team */
        $team = Filament::getTenant();

        return $table
            ->relationship(fn(): BelongsToMany => $team->members())
            ->heading("Users")
            ->columns([
                TextColumn::make('email'),
                TextColumn::make('name'),
                TextColumn::make('is_admin'),
            ])
            ->filters([
                // ...
            ])
            ->headerActions([
                Action::make('invite user')
                    ->schema([
                        TextInput::make('email')->email()->required(),
                        Checkbox::make('is_admin'),
                    ])
                    ->action(function (array $data) use ($team) {
                        try {
                            $team->inviteUser($data);
                        } catch (Throwable $e) {
                            Notification::make()
                                ->title('Error')
                                ->body($e->getMessage())
                                ->status('danger')
                                ->send();
                        }
                    }),
            ])
            ->recordActions([
                // ...
            ])
            ->toolbarActions([
                // ...
            ]);
    }
}
