<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Configuracion;
use COM;

class ActualizarTasaBcv extends Component
{
    
    #[On('bcv-update')]
    public function updatePostList(Configuracion $config)
    {
        $this->render();
    }
    public function render()
    {
        $tasa = Configuracion::first()->tasa_bcv;
        return view('livewire.actualizar-tasa-bcv', compact('tasa'));
    }
}