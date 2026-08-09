@extends('layouts.app')

@section('content')
    <livewire:payment-order-form :purchase-request="$purchaseRequest" />
@endsection