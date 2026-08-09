<?php

namespace App\Http\Controllers;
use App\Models\PurchaseRequest;
use Illuminate\Http\Request;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        return view('purchase_requests.index');
    }

    public function create()
    {
        return view('purchase_requests.create');
    }

    public function store(Request $request)
    {
        PurchaseRequest::create($request->validate($this->rules()));

        $request->session()->flash('success', 'Solicitação criada com sucesso.');

        return redirect()->route('purchase_requests.index');
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        return view('purchase_requests.edit', ['requestItem' => $purchaseRequest]);
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        return view('purchase_requests.show', ['requestItem' => $purchaseRequest]);
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->update($request->validate($this->rules()));

        $request->session()->flash('success', 'Solicitação atualizada com sucesso.');

        return redirect()->route('purchase_requests.index');
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        $purchaseRequest->delete();

        session()->flash('success', 'Solicitação excluída com sucesso.');

        return redirect()->route('purchase_requests.index');
    }

    private function rules(): array
    {
        return [
            'description' => 'required|string|max:255',
            'service_order_id' => 'nullable|exists:service_orders,id',
            'status' => 'required|in:pending,approved,rejected',
        ];
    }
}
