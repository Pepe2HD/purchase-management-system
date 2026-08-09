<?php

use App\Models\PurchaseRequest;
use App\Models\ServiceOrder;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{
    #[Locked]
    public ?int $purchaseRequestId = null;

    public $description = '';

    public $service_order_id = '';

    public $status = 'pending';

    public function mount(?PurchaseRequest $purchaseRequest = null): void
    {
        if ($purchaseRequest) {
            $this->purchaseRequestId = $purchaseRequest->id;
            $this->description = $purchaseRequest->description;
            $this->service_order_id = $purchaseRequest->service_order_id;
            $this->status = $purchaseRequest->status;
        }
    }

    public function save()
    {
        $validated = $this->validate([
            'description' => 'required|string|max:255',
            'service_order_id' => 'nullable|exists:service_orders,id',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $data = [
            'description' => $validated['description'],
            'service_order_id' => $validated['service_order_id'] ?: null,
            'status' => $validated['status'],
        ];

        if ($this->purchaseRequestId) {
            $purchaseRequest = PurchaseRequest::findOrFail($this->purchaseRequestId);
            $purchaseRequest->update($data);

            session()->flash('success', 'Solicitação atualizada com sucesso.');
        } else {
            PurchaseRequest::create($data);

            session()->flash('success', 'Solicitação criada com sucesso.');
        }

        return $this->redirectRoute('purchase_requests.index');
    }

    protected function messages(): array
    {
        return [
            'description.required' => 'O campo descrição é obrigatório.',
            'description.string' => 'A descrição deve ser um texto válido.',
            'description.max' => 'A descrição não pode ultrapassar 255 caracteres.',
            'service_order_id.exists' => 'Selecione uma ordem de serviço válida.',
            'status.required' => 'Selecione um status válido.',
            'status.in' => 'Selecione um status válido.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'description' => 'descrição',
            'service_order_id' => 'ordem de serviço',
            'status' => 'status',
        ];
    }

    public function render()
    {
        return $this->view([
            'serviceOrders' => ServiceOrder::orderBy('code')->get(),
            'isEditing' => filled($this->purchaseRequestId),
            'formTitle' => $this->purchaseRequestId ? 'Editar Solicitação' : 'Nova Solicitação',
            'cancelUrl' => $this->purchaseRequestId
                ? route('purchase_requests.show', $this->purchaseRequestId)
                : route('purchase_requests.index'),
        ]);
    }
};
?>

<div>
    <div class="form-card">
        <div class="form-header">
            <div>
                <h1 class="form-title">{{ $formTitle }}</h1>
                <p class="page-subtitle">Preencha os dados da solicitação de compra.</p>
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
                <label class="label" for="description">Descrição</label>
                <input
                    class="input @error('description') input-error @enderror"
                    type="text"
                    id="description"
                    wire:model="description"
                    placeholder="Ex.: Compra de equipamentos"
                >

                @error('description')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label class="label" for="service_order_id">Ordem de Serviço</label>
                <select
                    class="select @error('service_order_id') select-error @enderror"
                    id="service_order_id"
                    wire:model="service_order_id"
                >
                    <option value="">Nenhuma</option>
                    @foreach ($serviceOrders as $serviceOrder)
                        <option value="{{ $serviceOrder->id }}">
                            {{ $serviceOrder->code }}
                        </option>
                    @endforeach
                </select>

                @error('service_order_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="field">
                <label class="label" for="status">Status</label>
                <select
                    class="select @error('status') select-error @enderror"
                    id="status"
                    wire:model="status"
                >
                    <option value="pending">Pendente</option>
                    <option value="approved">Aprovada</option>
                    <option value="rejected">Rejeitada</option>
                </select>

                @error('status')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="responsive-actions">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Atualizar' : 'Salvar' }}</span>
                    <span wire:loading wire:target="save">Salvando...</span>
                </button>

                <a href="{{ $cancelUrl }}" class="btn btn-secondary">Voltar</a>
            </div>
        </form>
    </div>
</div>