<?php

use App\Models\PaymentOrder;
use App\Models\PurchaseRequest;
use App\Models\Installment;
use Carbon\Carbon;
use Livewire\Attributes\Locked;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

new class extends Component
{
    #[Locked]
    public int $purchaseRequestId;

    public string $type = 'cash';

    public $total_amount = '';

    public $installments_count = 1;

    public function mount(PurchaseRequest $purchaseRequest): void
    {
        $this->purchaseRequestId = $purchaseRequest->id;
    }

    public function save()
    {
        $validated = $this->validate([
            'type' => 'required|in:cash,installment',
            'total_amount' => 'required|numeric|gt:0',
            'installments_count' => 'required_if:type,installment|integer|gt:0',
        ]);

        DB::transaction(function () use ($validated) {
            $paymentOrder = PaymentOrder::create([
                'purchase_request_id' => $this->purchaseRequestId,
                'type' => $validated['type'],
                'total_amount' => $validated['total_amount'],
            ]);

            $installmentsCount = $validated['type'] === 'installment'
                ? (int) $validated['installments_count']
                : 1;

            $totalCents = (int) round(((float) $validated['total_amount']) * 100);
            $baseCents = intdiv($totalCents, $installmentsCount);
            $remainderCents = $totalCents % $installmentsCount;

            for ($index = 1; $index <= $installmentsCount; $index++) {
                $currentCents = $baseCents + ($index <= $remainderCents ? 1 : 0);

                Installment::create([
                    'payment_order_id' => $paymentOrder->id,
                    'installment_number' => $index,
                    'amount' => number_format($currentCents / 100, 2, '.', ''),
                    'due_date' => Carbon::today()->addDays(30 * ($index - 1))->toDateString(),
                    'status' => 'pending',
                ]);
            }
        });

        session()->flash('success', 'Pagamento criado com sucesso.');

        return $this->redirectRoute('purchase_requests.show', $this->purchaseRequestId);
    }

    protected function messages(): array
    {
        return [
            'type.required' => 'Selecione um tipo de pagamento válido.',
            'type.in' => 'Selecione um tipo de pagamento válido.',
            'total_amount.required' => 'O valor total é obrigatório.',
            'total_amount.numeric' => 'O valor total deve ser numérico.',
            'total_amount.gt' => 'O valor deve ser maior que zero.',
            'installments_count.required_if' => 'Informe o número de parcelas.',
            'installments_count.integer' => 'O número de parcelas deve ser um número inteiro.',
            'installments_count.gt' => 'O número de parcelas deve ser maior que zero.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'type' => 'tipo de pagamento',
            'total_amount' => 'valor total',
            'installments_count' => 'número de parcelas',
        ];
    }

    public function render()
    {
        return $this->view([
            'isInstallment' => $this->type === 'installment',
            'cancelUrl' => route('purchase_requests.show', $this->purchaseRequestId),
        ]);
    }
};
?>

<div>
    <div class="form-card">
        <div class="form-header">
            <div>
                <h1 class="form-title">Novo pagamento</h1>
                <p class="page-subtitle">Adicione uma ordem de pagamento para esta solicitação.</p>
            </div>

            <div class="page-actions">
                <a href="{{ $cancelUrl }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <form class="grid-form" wire:submit.prevent="save">
            <div class="field">
                <label class="label" for="type">Tipo de pagamento</label>
                <select class="select @error('type') select-error @enderror" id="type" wire:model.live="type">
                    <option value="cash">À vista</option>
                    <option value="installment">Parcelado</option>
                </select>

                @error('type')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label class="label" for="total_amount">Valor total</label>
                <input class="input @error('total_amount') input-error @enderror" type="number" step="0.01" min="0.01" id="total_amount" wire:model="total_amount" placeholder="Ex.: 1500.00">

                @error('total_amount')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            @if ($isInstallment)
                <div class="field">
                    <label class="label" for="installments_count">Número de parcelas</label>
                    <input class="input @error('installments_count') input-error @enderror" type="number" step="1" min="1" id="installments_count" wire:model="installments_count" placeholder="Ex.: 3">

                    @error('installments_count')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            @endif

            <div class="responsive-actions">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">Salvar pagamento</span>
                    <span wire:loading wire:target="save">Salvando...</span>
                </button>

                <a href="{{ $cancelUrl }}" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
    </div>
</div>