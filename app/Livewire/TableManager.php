<?php

namespace App\Livewire;

use App\Models\EventTable;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class TableManager extends Component
{
    public $name = '';

    public function render()
    {
        // Ordenamos por ID para que salgan Mesa 1, Mesa 2... en orden
        return view('livewire.table-manager', [
            'tables' => EventTable::orderBy('id', 'asc')->get(),
        ]);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|min:2',
        ], [
            'name.required' => 'Ponle un nombre a la mesa.',
        ]);

        EventTable::create([
            'name' => $this->name,
            'uuid' => (string) Str::uuid(), // Generamos el código secreto aquí
        ]);

        $this->name = ''; // Limpiamos input
        session()->flash('message', 'Mesa agregada correctamente.');
    }

    public function delete($id)
    {
        $table = EventTable::find($id);
        if ($table) {
            // Opcional: Podrías chequear si tiene deudas antes de borrar,
            // pero para hacerlo rápido permitimos borrar.
            $table->delete();
            session()->flash('message', 'Mesa eliminada.');
        }
    }
}
