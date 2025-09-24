<?php

namespace App\Filament\Actions\Campaign;

use App\Models\Campaign;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Livewire\Component;

use function App\Tools\errorNotif;
use function App\Tools\successNotif;

class ImportRecipients extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'campaign-recipient-import';
    }

    public function init(Campaign $campaign): static
    {
        $this->label("Import recipients");

        $this->schema([
            FileUpload::make('csv_file')
                ->label("CSV file")
                ->required()
                ->acceptedFileTypes(['text/csv']),
        ]);

        $this->action(function (array $data, Action $action, Component $livewire) use ($campaign) {
            try {
                $campaign->importCsv($data['csv_file']);
                successNotif('Recipients imported successfully');
                return $livewire->redirect(request()->header('Referer'));
            } catch (Exception $e) {
                errorNotif($e->getMessage());
                $action->cancel();
            }
        });

        return $this;
    }
}
