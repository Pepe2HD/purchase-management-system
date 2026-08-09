<?php

use App\Models\Installment;
use App\Models\PurchaseRequest;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{
    #[Locked]
    public int $purchaseRequestId;

    public ?string $feedbackMessage = null;

    public function mount(PurchaseRequest $purchaseRequest): void
    {
        $this->purchaseRequestId = $purchaseRequest->id;
    }

    public function render()
    {
        $purchaseRequest = PurchaseRequest::with(['serviceOrder', 'paymentOrders.installments'])
            ->findOrFail($this->purchaseRequestId);

        return $this->view([
            'purchaseRequest' => $purchaseRequest,
            'totalPayments' => $purchaseRequest->paymentOrders->sum('total_amount'),
        ]);
    }

    public function markAsPaid(int $installmentId): void
    {
        $installment = Installment::whereHas('paymentOrder', function ($query) {
            $query->where('purchase_request_id', $this->purchaseRequestId);
        })->findOrFail($installmentId);

        if ($installment->status === 'paid') {
            return;
        }

        $installment->update(['status' => 'paid']);

        $this->feedbackMessage = 'Parcela marcada como paga com sucesso.';
    }
};
?>

<div>
    <div class="stack">
        <div class="page-card">
            <div class="page-top">
                <div>
                    <h1 class="page-title">Solicitação #{{ $purchaseRequest->id }}</h1>
                    <p class="page-subtitle">Detalhes da solicitação e suas ordens de pagamento.</p>
                </div>

                <div class="page-actions">
                    <a href="{{ route('purchase_requests.index') }}" class="btn btn-secondary">Voltar</a>
                    <a href="{{ route('purchase_requests.edit', $purchaseRequest->id) }}" class="btn btn-secondary">Editar</a>
                    <a href="{{ route('purchase_requests.payment_orders.create', $purchaseRequest->id) }}" class="btn btn-primary">Adicionar pagamento</a>
                </div>
            </div>

            @if (session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            @if ($feedbackMessage)
                <div class="alert-success">{{ $feedbackMessage }}</div>
            @endif

            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Descrição:</span>
                    <span>{{ $purchaseRequest->description }}</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Status:</span>
                    @php
                        $statusClass = match ($purchaseRequest->status) {
                            'pending' => 'badge badge-pending',
                            'approved' => 'badge badge-approved',
                            'rejected' => 'badge badge-rejected',
                            default => 'badge',
                        };
                        $statusLabel = match ($purchaseRequest->status) {
                            'pending' => 'Pendente',
                            'approved' => 'Aprovada',
                            'rejected' => 'Rejeitada',
                            default => $purchaseRequest->status,
                        };
                    @endphp
                    <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Ordem de Serviço:</span>
                    <span>{{ $purchaseRequest->serviceOrder?->code ?? 'Nenhuma' }}</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Data de criação:</span>
                    <span>{{ $purchaseRequest->created_at?->format('d/m/Y H:i') }}</span>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Total dos pagamentos:</span>
                    <span>R$ {{ number_format((float) $totalPayments, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <section class="section-card">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Ordens de Pagamento</h2>
                    <p class="page-subtitle">Cada pagamento pode conter uma ou mais parcelas.</p>
                </div>

                <div class="page-actions">
                    <a href="{{ route('purchase_requests.payment_orders.create', $purchaseRequest->id) }}" class="btn btn-primary">Adicionar pagamento</a>
                </div>
            </div>

            @forelse ($purchaseRequest->paymentOrders as $paymentOrder)
                <article class="payment-card" wire:key="payment-order-{{ $paymentOrder->id }}">
                    <div class="card-header">
                        <div>
                            <h3 class="card-title">Pagamento #{{ $paymentOrder->id }}</h3>
                            <p class="page-subtitle">Criado em {{ $paymentOrder->created_at?->format('d/m/Y H:i') }}</p>
                        </div>

                        <div class="payment-meta">
                            <span class="badge {{ $paymentOrder->type === 'cash' ? 'badge-approved' : 'badge-pending' }}">
                                {{ $paymentOrder->type === 'cash' ? 'À vista' : 'Parcelado' }}
                            </span>
                            <span class="muted">Total: R$ {{ number_format((float) $paymentOrder->total_amount, 2, ',', '.') }}</span>
                        </div>
                    </div>

                    <h4 class="subsection-title">Parcelas</h4>

                    @if ($paymentOrder->installments->isNotEmpty())
                        <div class="table-wrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Parcela</th>
                                        <th>Valor</th>
                                        <th>Vencimento</th>
                                        <th>Status</th>
                                        <th>Ação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($paymentOrder->installments as $installment)
                                        @php
                                            $installmentStatusClass = $installment->status === 'paid' ? 'badge badge-paid' : 'badge badge-pending';
                                            $installmentStatusLabel = $installment->status === 'paid' ? 'Pago' : 'Pendente';
                                        @endphp
                                        <tr wire:key="installment-{{ $installment->id }}">
                                            <td>{{ $installment->installment_number }}</td>
                                            <td>R$ {{ number_format((float) $installment->amount, 2, ',', '.') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($installment->due_date)->format('d/m/Y') }}</td>
                                            <td><span class="{{ $installmentStatusClass }}">{{ $installmentStatusLabel }}</span></td>
                                            <td>
                                                @if ($installment->status === 'pending')
                                                    <button
                                                        type="button"
                                                        class="btn btn-secondary"
                                                        wire:click="markAsPaid({{ $installment->id }})"
                                                        wire:confirm="Tem certeza que deseja marcar esta parcela como paga?"
                                                        wire:loading.attr="disabled"
                                                        wire:target="markAsPaid"
                                                    >
                                                        Marcar como pago
                                                    </button>
                                                @else
                                                    <span class="muted">Parcela já paga</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">Nenhuma parcela encontrada.</div>
                    @endif
                </article>
            @empty
                <div class="empty-state">Nenhuma ordem de pagamento vinculada a esta solicitação.</div>
            @endforelse
        </section>
    </div>
</div>