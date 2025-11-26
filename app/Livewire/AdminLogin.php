<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class AdminLogin extends Component
{
    public $pin = '';
    public $error = '';

    public function login()
    {
        $masterPin = env('ADMIN_PIN', '2468');

        if ($this->pin === $masterPin) {
            session(['admin_access' => true]);

            // Redirigimos al dashboard
            return redirect()->route('dashboard');
        } else {
            $this->error = 'PIN Incorrecto ⛔';
            $this->pin = ''; // Limpiamos el campo
        }
    }

    public function render()
    {
        return view('livewire.admin-login');
    }
}
