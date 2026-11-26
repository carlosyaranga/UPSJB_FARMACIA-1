@extends('template.administrador')

@section('title', 'Editar venta')
@section('name_section', 'Editar venta')
@section('description', 'Modificar productos y cantidades')
@section('item_section', 'Ventas')
@section('route_section', route('sales.index'))
@section('section', 'Ventas')
@section('module', 'Gestión')

@section('content')
<div class="card bg-white border-0 shadow-sm border-5 border-top border-secondary">
    <div class="card-body">
        <form action="{{ route('sales.update', $sale) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <label for="customer_id" class="form-label">Cliente</label>
                    <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror">
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(old('customer_id', $sale->customer_id) == $customer->id)>{{ $customer->name }} - DNI {{ $customer->dni }}</option>
                        @endforeach
                    </select>
                    @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6 mb-3 d-flex align-items-end">
                    <div class="alert alert-light border w-100 mb-0"><small class="text-muted d-block">Vendedor</small><strong>{{ $sale->user->name }}</strong></div>
                </div>
            </div>

            @if ($errors->has('items'))<div class="alert alert-danger py-2">{{ $errors->first('items') }}</div>@endif

            <table class="table table-sm align-middle">
                <thead><tr><th class="small text-uppercase">Medicamento</th><th class="small text-uppercase text-center" style="width:120px">Cantidad</th><th class="small text-uppercase text-end" style="width:120px">Precio</th><th class="small text-uppercase text-end" style="width:120px">Subtotal</th><th style="width:50px"></th></tr></thead>
                <tbody id="items">
                    @foreach (old('items', $sale->details->map(fn ($detail) => ['medicine_id' => $detail->medicine_id, 'amount' => $detail->amount])->values()->all()) as $i => $item)
                        @include('management.sales.item-row', ['index' => $i, 'item' => $item])
                    @endforeach
                </tbody>
                <tfoot><tr><td colspan="3" class="text-end fw-semibold">Total (incluye IGV)</td><td class="text-end fw-bold" id="total">S/ 0.00</td><td></td></tr></tfoot>
            </table>

            <button type="button" id="add-item" class="btn btn-outline-primary btn-sm mb-4"><i class="bi bi-plus-lg me-1"></i>Agregar medicamento</button>
            <div><button type="submit" class="btn btn-primary text-white">Actualizar venta</button><a href="{{ route('sales.show', $sale) }}" class="btn btn-secondary">Cancelar</a></div>
        </form>
    </div>
</div>

<template id="item-template">@include('management.sales.item-row', ['index' => '__INDEX__', 'item' => []])</template>
@endsection

@push('scripts')
<script>
const tbody=document.getElementById('items');
const template=document.getElementById('item-template').innerHTML;
let index={{ count(old('items', $sale->details)) }};
function recalculate(){let total=0;tbody.querySelectorAll('tr').forEach(row=>{const option=row.querySelector('select').selectedOptions[0];const price=parseFloat(option?.dataset.price||0);const amount=parseInt(row.querySelector('input').value||0);const subtotal=price*amount;row.querySelector('.price').textContent='S/ '+price.toFixed(2);row.querySelector('.subtotal').textContent='S/ '+subtotal.toFixed(2);total+=subtotal;});document.getElementById('total').textContent='S/ '+total.toFixed(2);}
document.getElementById('add-item').addEventListener('click',()=>tbody.insertAdjacentHTML('beforeend',template.replaceAll('__INDEX__',index++)));
tbody.addEventListener('click',e=>{if(e.target.closest('.remove-item')){e.target.closest('tr').remove();recalculate();}});
tbody.addEventListener('input',recalculate);
recalculate();
</script>
@endpush
