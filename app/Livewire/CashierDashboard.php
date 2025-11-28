<?php

namespace App\Livewire;

use App\Models\EventTable;
use App\Models\Order;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
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
        // 1. Totales Generales (SOLO DEL TURNO ACTUAL)
        // Filtramos por 'is_closed' => false
        $totalCash = Payment::where('method', 'cash')->where('is_closed', false)->sum('amount');
        $totalTransfer = Payment::where('method', 'transfer')->where('is_closed', false)->sum('amount');

        // 2. Mesas Abiertas
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

    // --- CIERRE DE CAJA ---
    public function closeRegister()
    {
        // 1. Calculamos los totales finales antes de cerrar
        $payments = Payment::where('is_closed', false)->get();

        if ($payments->isEmpty()) {
            session()->flash('error', 'No hay movimientos para cerrar.');

            return;
        }

        $cash = $payments->where('method', 'cash')->sum('amount');
        $transfer = $payments->where('method', 'transfer')->sum('amount');
        $total = $cash + $transfer;
        $date = Carbon::now()->format('d/m/Y H:i');

        $data = [
            'date' => $date,
            'cash' => $cash,
            'transfer' => $transfer,
            'total' => $total,
            'count' => $payments->count(),
        ];

        $pdf = Pdf::loadView('pdf.cierre', $data);

        Payment::where('is_closed', false)->update(['is_closed' => true]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Cierre-Caja-'.Carbon::now()->format('dmY-Hi').'.pdf');

        // 2. Contenido del Ticket (Texto Plano)
        $content = "=== CIERRE DE CAJA ===\n";
        $content .= "Fecha: $date\n";
        $content .= "----------------------\n";
        $content .= 'Efectivo:      $ '.number_format($cash, 2)."\n";
        $content .= 'Transferencia: $ '.number_format($transfer, 2)."\n";
        $content .= "----------------------\n";
        $content .= 'TOTAL:         $ '.number_format($total, 2)."\n";
        $content .= "----------------------\n";
        $content .= 'Movimientos:   '.$payments->count()."\n";
        $content .= "======================\n";

        // 3. Cerramos los pagos en BD (Reiniciar ciclo)
        Payment::where('is_closed', false)->update(['is_closed' => true]);

        // 4. Descargamos el archivo
        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'cierre-caja-'.Carbon::now()->format('Hi').'.txt');
    }
}
