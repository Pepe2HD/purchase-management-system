<?php

use App\Models\PurchaseRequest;
use Livewire\Component;

new class extends Component
{
    public ?string $feedbackMessage = null;

    public function delete($id)
    {
        $request = PurchaseRequest::findOrFail($id);

        $request->delete();

        $this->feedbackMessage = 'Solicitação excluída com sucesso.';
    }

    public function render()
    {
        return $this->view([
            'requests' => PurchaseRequest::with('serviceOrder')->latest()->get(),
        ]);
    }
};
?>

<div>
    <div class="page-card">
        <div class="page-header">
            <div>
                <h1 class="page-title">Solicitações de Compra</h1>
                <p class="page-subtitle">Gerencie as solicitações cadastradas no sistema.</p>
            </div>

            <div class="page-actions">
                <a href="{{ route('purchase_requests.create') }}" class="btn btn-primary">Nova Solicitação</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if ($feedbackMessage)
            <div class="alert-success">{{ $feedbackMessage }}</div>
        @endif

        @if ($requests->isNotEmpty())
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descrição</th>
                            <th>Status</th>
                            <th>Ordem de Serviço</th>
                            <th>Data de Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $req)
                            @php
                                $statusClass = match ($req->status) {
                                    'pending' => 'badge badge-pending',
                                    'approved' => 'badge badge-approved',
                                    'rejected' => 'badge badge-rejected',
                                    default => 'badge',
                                };

                                $statusLabel = match ($req->status) {
                                    'pending' => 'Pendente',
                                    'approved' => 'Aprovada',
                                    'rejected' => 'Rejeitada',
                                    default => $req->status,
                                };
                            @endphp

                            <tr>
                                <td>{{ $req->id }}</td>
                                <td>{{ $req->description }}</td>
                                <td><span class="{{ $statusClass }}">{{ $statusLabel }}</span></td>
                                <td>{{ $req->serviceOrder?->code ?? 'Sem ordem de serviço' }}</td>
                                <td>{{ $req->created_at?->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="row-actions">
                                        <a href="{{ route('purchase_requests.show', $req->id) }}" class="btn btn-secondary">Visualizar</a>
                                        <a href="{{ route('purchase_requests.edit', $req->id) }}" class="btn btn-secondary">Editar</a>
                                        <button
                                            type="button"
                                            class="btn btn-danger"
                                            wire:click="delete({{ $req->id }})"
                                            wire:confirm="Tem certeza que deseja excluir esta solicitação?"
                                            wire:loading.attr="disabled"
                                            wire:target="delete"
                                        >
                                            Excluir
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">Nenhuma solicitação de compra cadastrada.</div>
        @endif
    </div>
</div>