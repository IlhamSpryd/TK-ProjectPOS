<?php

namespace App\Livewire\Settings;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Appearance settings')]
class Appearance extends Component
{
    public function render()
    {
        $view = 'livewire.settings.appearance';
        return view($view)->layout('components.layouts.app', [
            'title' => 'Pengaturan',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'route' => route('dashboard')],
                ['label' => 'Pengaturan'],
            ],
        ]);
    }
}
