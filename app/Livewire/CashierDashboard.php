<?php

namespace App\Livewire;

use App\Models\EventTable;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.app')]
class CashierDashboard extends Component
{
    public $selectedTable = null;

    public $cashAmount = 0;

    public $transferAmount = 0;

    public function render()
    {
        $totalCash = Payment::where('method', 'cash')->sum('amount');
        $totalTransfer = Payment::where('method', 'transfer')->sum('amount');

        // 2. Mesas Abiertas (Lo que ya tenías)
        $tables = EventTable::whereHas('orders', function ($q) {
            $q->where('is_paid', false);
        })->with(['orders' => function ($q) {
            $q->where('is_paid', false)->with('items');
        }])->get();

        return view('livewire.cashier-dashboard', [
            'tables' => $tables,
            'stats' => [
                'cash' => $totalCash,
                'transfer' => $totalTransfer,
                'total' => $totalCash + $totalTransfer,
            ],
        ]);
    }

    public function selectTable($tableId)
    {
        $this->selectedTable = EventTable::with(['orders' => function ($q) {
            $q->where('is_paid', false)->with('items');
        }])->find($tableId);

        $this->cashAmount = 0;
        $this->transferAmount = 0;
    }

    public function closeTable()
    {
        if (! $this->selectedTable) {
            return;
        }

        // 1. Calcular el total de la deuda
        $totalDebt = $this->selectedTable->orders->sum(fn ($o) => $o->total);
        $totalPaid = $this->cashAmount + $this->transferAmount;

        // Validación simple: ¿Están pagando todo?
        // (Podrías permitir pago parcial, pero para simplificar obligamos a saldar)
        if ($totalPaid < $totalDebt) {
            session()->flash('error', 'El monto ingresado es menor al total ($'.number_format($totalDebt).')');

            return;
        }

        DB::transaction(function () {
            // A. Registrar el Pago
            if ($this->cashAmount > 0) {
                Payment::create([
                    'event_table_id' => $this->selectedTable->id,
                    'amount' => $this->cashAmount,
                    'method' => 'cash',
                ]);
            }
            if ($this->transferAmount > 0) {
                Payment::create([
                    'event_table_id' => $this->selectedTable->id,
                    'amount' => $this->transferAmount,
                    'method' => 'transfer',
                ]);
            }

            // B. Marcar órdenes como pagadas
            Order::where('event_table_id', $this->selectedTable->id)
                ->where('is_paid', false)
                ->update(['is_paid' => true]);
        });

        $this->selectedTable = null;
        session()->flash('message', 'Mesa cerrada y cobrada exitosamente. 💰');
    }
}
