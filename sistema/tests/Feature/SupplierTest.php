<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\SupplierSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->nullable();
            $table->string('name');
            $table->string('dni')->nullable();
            $table->string('phone')->nullable();
            $table->string('tuition')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('ruc', 11)->unique();
            $table->string('phone', 9)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('address', 150)->nullable();
            $table->timestamps();
        });
    }

    public function test_ruc_must_have_eleven_digits_and_shows_the_spanish_message(): void
    {
        config(['app.locale' => 'es']);
        $this->actingAs($this->userWithRole('Químico farmacéutico'));

        $response = $this->from(route('suppliers.create'))->post(route('suppliers.store'), [
            'name' => 'Proveedor de prueba',
            'ruc' => '1234567890',
        ]);

        $response->assertRedirect(route('suppliers.create'))
            ->assertSessionHasErrors(['ruc']);
        $this->assertSame(
            'El campo RUC debe tener 11 dígitos.',
            session('errors')->getBag('default')->first('ruc'),
        );
        $this->assertDatabaseCount('suppliers', 0);
    }

    public function test_ruc_is_unique_but_a_supplier_can_be_updated_without_changing_its_ruc(): void
    {
        $this->actingAs($this->userWithRole('Administrador'));
        $supplier = Supplier::create([
            'name' => 'Proveedor existente',
            'ruc' => '20100000001',
        ]);

        $duplicateResponse = $this->from(route('suppliers.create'))->post(route('suppliers.store'), [
            'name' => 'Otro proveedor',
            'ruc' => '20100000001',
        ]);

        $duplicateResponse->assertRedirect(route('suppliers.create'))
            ->assertSessionHasErrors(['ruc']);

        $updateResponse = $this->put(route('suppliers.update', $supplier), [
            'name' => 'Proveedor actualizado',
            'ruc' => '20100000001',
        ]);

        $updateResponse->assertRedirect(route('suppliers.index'))
            ->assertSessionDoesntHaveErrors();
        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Proveedor actualizado',
            'ruc' => '20100000001',
        ]);
        $this->assertDatabaseCount('suppliers', 1);
        $this->get(route('suppliers.index'))->assertOk()->assertSee('Proveedor actualizado');
        $this->get(route('suppliers.create'))->assertOk();
        $this->get(route('suppliers.edit', $supplier))->assertOk();
    }

    public function test_supplier_can_be_created_and_deleted(): void
    {
        $this->actingAs($this->userWithRole('Administrador'));

        $storeResponse = $this->post(route('suppliers.store'), [
            'name' => 'Proveedor temporal',
            'ruc' => '20987654321',
        ]);

        $storeResponse->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', [
            'name' => 'Proveedor temporal',
            'ruc' => '20987654321',
        ]);

        $supplier = Supplier::where('ruc', '20987654321')->firstOrFail();
        $deleteResponse = $this->delete(route('suppliers.destroy', $supplier));

        $deleteResponse->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    public function test_cashier_cannot_access_suppliers_and_does_not_see_the_menu_link(): void
    {
        $this->actingAs($this->userWithRole('Cajero'));

        $this->get(route('suppliers.index'))->assertForbidden();
        $this->view('partials.sidebar')->assertDontSee('Proveedores');
    }

    public function test_supplier_access_redirects_to_login_and_admin_can_log_in_and_log_out(): void
    {
        $user = $this->userWithRole('Administrador');

        $this->get(route('suppliers.index'))->assertRedirect(route('login'));
        $this->get(route('login'))->assertOk()
            ->assertSee('Iniciar sesión')
            ->assertSee('Gestiona tu farmacia de forma simple y segura.');

        $loginResponse = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $loginResponse->assertRedirect(route('suppliers.index'));
        $this->get(route('suppliers.index'))->assertOk()->assertSee('Proveedores');
        $this->post(route('logout'))
            ->assertRedirect(route('login'))
            ->assertSessionHas('ok', 'Cerraste sesión correctamente.');
        $this->get(route('suppliers.index'))->assertRedirect(route('login'));
    }

    public function test_dashboard_and_resource_pages_require_authentication(): void
    {
        $protectedRoutes = [
            'dashboard.index',
            'categories.index',
            'medicines.index',
            'customers.index',
            'sales.index',
            'roles.index',
            'users.index',
            'suppliers.index',
        ];

        foreach ($protectedRoutes as $routeName) {
            $this->get(route($routeName))->assertRedirect(route('login'));
        }
    }

    public function test_authenticated_user_is_redirected_from_login_and_sidebar_shows_account_details(): void
    {
        $user = $this->userWithRole('Administrador');
        $this->actingAs($user);

        $this->get(route('login'))->assertRedirect(route('dashboard.index'));
        $this->view('partials.sidebar')
            ->assertSee('US')
            ->assertSee($user->name)
            ->assertSee($user->email)
            ->assertSee('Administrador');
    }

    public function test_invalid_login_credentials_are_rejected(): void
    {
        $user = $this->userWithRole('Administrador');

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'incorrecta',
        ]);

        $response->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email']);
        $this->assertSame(
            'El correo o la contraseña no son correctos.',
            session('errors')->getBag('default')->first('email'),
        );
        $this->assertSame($user->email, session('_old_input.email'));
        $this->assertArrayNotHasKey('password', session('_old_input'));
    }

    public function test_valid_login_without_an_intended_url_redirects_to_dashboard(): void
    {
        $user = $this->userWithRole('Administrador');

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard.index'));
    }

    public function test_supplier_seeder_creates_four_suppliers(): void
    {
        (new SupplierSeeder)->run();

        $this->assertDatabaseCount('suppliers', 4);
    }

    private function userWithRole(string $roleName): User
    {
        $role = Role::create(['name' => $roleName]);

        return User::create([
            'role_id' => $role->id,
            'name' => 'Usuario de prueba',
            'dni' => '12345678',
            'email' => strtolower(str_replace(' ', '', $roleName)).'@example.test',
            'password' => 'password',
        ]);
    }
}
