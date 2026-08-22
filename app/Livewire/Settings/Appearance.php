<?php

namespace App\Livewire\Settings;

use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Appearance settings')]
class Appearance extends Component
{
    public function render()
    {
        return view('livewire.settings.appearance')->layout('components.layouts.app', [
            'title' => 'Pengaturan Tampilan',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'route' => route('dashboard')],
                ['label' => 'Pengaturan']
            ]
        ]);
    }
