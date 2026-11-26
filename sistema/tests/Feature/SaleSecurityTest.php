<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Medicine;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_sale_uses_authenticated_user_and_reduces_stock(): void
    {
        $role = Role::create(['name' => 'Cajero', 'description' => 'Ventas']);
        $seller = User::create([
            'role_id' => $role->id,
            'active' => true,
            'name' => 'Cajero Prueba',
            'dni' => '12345678',
            'email' => 'cajero@example.com',
            'password' => 'password123',
        ]);

        $category = Category::create(['name' => 'Analgésicos', 'description' => 'Prueba']);
        $medicine = Medicine::create([
            'category_id' => $category->id,
            'name' => 'Paracetamol',
            'price' => 5,
            'stock' => 10,
            'expiry_date' => now()->addYear()->toDateString(),
        ]);
        $customer = Customer::create(['name' => 'Cliente Prueba', 'dni' => '87654321']);

        $response = $this->actingAs($seller)->post(route('sales.store'), [
            'customer_id' => $customer->id,
            'items' => [['medicine_id' => $medicine->id, 'amount' => 2]],
        ]);

        $response->assertSessionHas('ok');
        $this->assertDatabaseHas('sales', [
            'customer_id' => $customer->id,
            'user_id' => $seller->id,
            'status' => 'active',
        ]);
        $this->assertDatabaseHas('medicines', ['id' => $medicine->id, 'stock' => 8]);
    }

    public function test_sale_rejects_insufficient_stock(): void
    {
        $role = Role::create(['name' => 'Cajero', 'description' => 'Ventas']);
        $seller = User::create([
            'role_id' => $role->id,
            'active' => true,
            'name' => 'Cajero',
            'dni' => '11111111',
            'email' => 'cajero2@example.com',
            'password' => 'password123',
        ]);
        $category = Category::create(['name' => 'Prueba', 'description' => 'Prueba']);
        $medicine = Medicine::create([
            'category_id' => $category->id,
            'name' => 'Sin stock',
            'price' => 1,
            'stock' => 1,
        ]);
        $customer = Customer::create(['name' => 'Cliente', 'dni' => '22222222']);

        $response = $this->actingAs($seller)->from(route('sales.create'))->post(route('sales.store'), [
            'customer_id' => $customer->id,
            'items' => [['medicine_id' => $medicine->id, 'amount' => 2]],
        ]);

        $response->assertSessionHasErrors('items');
        $this->assertDatabaseCount('sales', 0);
    }

    public function test_editing_sale_restores_old_stock_then_applies_new_amount(): void
    {
        [$seller, $customer, $category, $medicine] = $this->createSaleFixture();

        $sale = Sale::create([
            'customer_id' => $customer->id,
            'user_id' => $seller->id,
            'date' => now(),
            'total' => 6,
            'igv' => 0.92,
            'status' => 'active',
        ]);
        $sale->details()->create([
            'medicine_id' => $medicine->id,
            'amount' => 2,
            'price_unit' => 5,
            'subtotal' => 10,
        ]);
        $medicine->decrement('stock', 2);

        $response = $this->actingAs($seller)->put(route('sales.update', $sale), [
            'customer_id' => $customer->id,
            'items' => [['medicine_id' => $medicine->id, 'amount' => 1]],
        ]);

        $response->assertSessionHas('ok');
        $this->assertDatabaseHas('detail_sales', ['sale_id' => $sale->id, 'medicine_id' => $medicine->id, 'amount' => 1]);
        $this->assertDatabaseHas('medicines', ['id' => $medicine->id, 'stock' => 9]);
    }

    public function test_admin_can_cancel_sale_and_cancelled_sale_cannot_be_edited(): void
    {
        [$seller, $customer, $category, $medicine] = $this->createSaleFixture('Administrador');

        $sale = Sale::create([
            'customer_id' => $customer->id,
            'user_id' => $seller->id,
            'date' => now(),
            'total' => 10,
            'igv' => 1.53,
            'status' => 'active',
        ]);
        $sale->details()->create([
            'medicine_id' => $medicine->id,
            'amount' => 2,
            'price_unit' => 5,
            'subtotal' => 10,
        ]);
        $medicine->decrement('stock', 2);

        $this->actingAs($seller)->delete(route('sales.destroy', $sale))->assertSessionHas('ok');

        $this->assertDatabaseHas('sales', ['id' => $sale->id, 'status' => 'cancelled']);
        $this->assertDatabaseHas('medicines', ['id' => $medicine->id, 'stock' => 10]);

        $this->actingAs($seller)->put(route('sales.update', $sale), [
            'customer_id' => $customer->id,
            'items' => [['medicine_id' => $medicine->id, 'amount' => 1]],
        ])->assertSessionHas('error');
    }

    private function createSaleFixture(string $roleName = 'Cajero'): array
    {
        $role = Role::create(['name' => $roleName, 'description' => 'Prueba']);
        $seller = User::create([
            'role_id' => $role->id,
            'active' => true,
            'name' => 'Usuario '.$roleName,
            'dni' => substr(str_shuffle('1234567890'), 0, 8),
            'email' => strtolower(str_replace(' ', '.', $roleName)).uniqid().'@example.com',
            'password' => 'password123',
        ]);
        $category = Category::create(['name' => 'Prueba '.uniqid(), 'description' => 'Prueba']);
        $medicine = Medicine::create([
            'category_id' => $category->id,
            'name' => 'Medicamento '.uniqid(),
            'price' => 5,
            'stock' => 10,
            'expiry_date' => now()->addYear()->toDateString(),
        ]);
        $customer = Customer::create(['name' => 'Cliente '.uniqid(), 'dni' => substr(str_shuffle('1234567890'), 0, 8)]);

        return [$seller, $customer, $category, $medicine];
    }
}
