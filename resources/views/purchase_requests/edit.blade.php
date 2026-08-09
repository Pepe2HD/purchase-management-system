@extends('layouts.app')

@section('content')
    <livewire:purchase-request-form :purchase-request="$requestItem" />
@endsection