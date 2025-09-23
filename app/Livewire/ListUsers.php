<?php

namespace App\Livewire;

use App\Models\Team;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DetachAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Throwable;

use function App\Tools\errorNotif;

class ListUsers extends Component implements HasActions, HasSchemas, HasTable
{
    use InteractsWithActions;
    use InteractsWithSchemas;
    use InteractsWithTable;

    public function table(Table $table): Table
    {
        /** @var Team $team */
        $team = Filament::getTenant();
        $iAmAdmin = Auth::user()->isAdminOf($team);

        return $table
            ->relationship(fn(): BelongsToMany => $team->members())
            ->heading("Users")
            ->columns([
                TextColumn::make('email'),
                TextColumn::make('name'),
                ToggleColumn::make('is_admin')
                    ->disabled(fn(User $u) => $u->id == Auth::id() || !$iAmAdmin),
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
                            errorNotif($e->getMessage());
                        }
                    })->hidden(!$iAmAdmin),
            ])
            ->recordActions([
                DetachAction::make()
                    ->disabled(fn(User $u) => $u->id == Auth::id())
                    ->hidden(!$iAmAdmin),
            ])
            ->toolbarActions([
                // ...
            ]);
    }
}
