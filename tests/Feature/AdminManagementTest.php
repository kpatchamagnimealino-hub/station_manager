<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Station;
use App\Models\StationStock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_displays_low_stock_alerts(): void
    {
        $admin = User::factory()->create([
            'name' => 'Administrateur',
            'email' => 'admin@stationmanager.test',
            'is_admin' => true,
        ]);

        $station = Station::create([
            'name' => 'Bonanjo',
            'location' => 'Douala',
            'status' => 'active',
        ]);
        $product = Product::create([
            'name' => 'Super 95',
            'unit' => 'litre',
            'stock_quantity' => 80,
            'minimum_stock' => 100,
            'price' => 950,
        ]);
        StationStock::create([
            'station_id' => $station->id,
            'product_id' => $product->id,
            'quantity' => 80,
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Alerte stock faible')
            ->assertSee('Bonanjo · Super 95');
    }

    public function test_admin_can_download_excel_report(): void
    {
        $admin = User::factory()->create([
            'name' => 'Administrateur',
            'email' => 'admin2@stationmanager.test',
            'is_admin' => true,
        ]);
        $station = Station::create([
            'name' => 'Bonanjo',
            'location' => 'Douala',
            'status' => 'active',
        ]);
        $product = Product::create([
            'name' => 'Super 95',
            'unit' => 'litre',
            'stock_quantity' => 0,
            'minimum_stock' => 100,
            'price' => 950,
        ]);
        StationStock::create([
            'station_id' => $station->id,
            'product_id' => $product->id,
            'quantity' => 80,
        ]);
        Sale::create([
            'station_id' => $station->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'unit_price' => 950,
            'sold_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/admin/reports/export-excel')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->assertSee('Bonanjo')
            ->assertSee('Super 95')
            ->assertSee('Ventes')
            ->assertSee('Stock');
    }

    public function test_admin_can_update_station_and_product_details_without_changing_stock(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $station = Station::create([
            'name' => 'Bonanjo',
            'location' => 'Douala',
            'status' => 'active',
        ]);
        $product = Product::create([
            'name' => 'Super 95',
            'unit' => 'litre',
            'stock_quantity' => 0,
            'minimum_stock' => 100,
            'price' => 950,
        ]);
        StationStock::create([
            'station_id' => $station->id,
            'product_id' => $product->id,
            'quantity' => 75,
        ]);

        $this->actingAs($admin)
            ->put('/admin/stations/'.$station->id, [
                'name' => 'Bonanjo Centre',
                'location' => 'Douala',
                'status' => 'inactive',
            ])
            ->assertRedirect('/admin/stations');

        $this->put('/admin/products/'.$product->id, [
            'name' => 'Super 95+',
            'unit' => 'litre',
            'minimum_stock' => 120,
            'price' => 975,
            'description' => 'Formule nouvelle',
        ])->assertRedirect('/admin/products');

        $this->assertDatabaseHas('stations', [
            'id' => $station->id,
            'name' => 'Bonanjo Centre',
            'status' => 'inactive',
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Super 95+',
            'minimum_stock' => 120,
        ]);
        $this->assertDatabaseHas('station_stocks', [
            'station_id' => $station->id,
            'product_id' => $product->id,
            'quantity' => 75,
        ]);
    }

    public function test_admin_can_download_pdf_stock_and_sales_report(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)
            ->get('/admin/reports/export-pdf')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $pdf = $response->getContent();
        preg_match('/startxref\s+(\d+)/', $pdf, $matches);

        $this->assertStringStartsWith('%PDF-1.4', $pdf);
        $this->assertNotEmpty($matches[1] ?? null);
        $this->assertStringStartsWith("xref\n", substr($pdf, (int) $matches[1]));
    }

    public function test_general_director_sees_sales_and_stock_by_station(): void
    {
        $director = User::factory()->create([
            'role' => User::ROLE_GENERAL_DIRECTOR,
        ]);
        $station = Station::create([
            'name' => 'Bonanjo',
            'location' => 'Douala',
            'status' => 'active',
        ]);
        $product = Product::create([
            'name' => 'Super 95',
            'unit' => 'litre',
            'stock_quantity' => 0,
            'minimum_stock' => 100,
            'price' => 950,
        ]);
        StationStock::create([
            'station_id' => $station->id,
            'product_id' => $product->id,
            'quantity' => 75,
        ]);
        Sale::create([
            'station_id' => $station->id,
            'product_id' => $product->id,
            'quantity' => 25,
            'unit_price' => 950,
            'sold_at' => now(),
        ]);

        $this->actingAs($director)
            ->get('/direction')
            ->assertOk()
            ->assertSee('Ventes du jour par station')
            ->assertSee('Bonanjo')
            ->assertSee('25 L')
            ->assertSee('23 750 FCFA')
            ->assertSee('Stocks disponibles par station')
            ->assertSee('75,00 litre');
    }

    public function test_daily_reports_are_generated_and_archived_for_download(): void
    {
        Storage::fake('local');
        $station = Station::create([
            'name' => 'Bonanjo',
            'location' => 'Douala',
            'status' => 'active',
        ]);
        $product = Product::create([
            'name' => 'Super 95',
            'unit' => 'litre',
            'stock_quantity' => 0,
            'minimum_stock' => 100,
            'price' => 950,
        ]);
        StationStock::create([
            'station_id' => $station->id,
            'product_id' => $product->id,
            'quantity' => 75,
        ]);
        Sale::create([
            'station_id' => $station->id,
            'product_id' => $product->id,
            'quantity' => 25,
            'unit_price' => 950,
            'sold_at' => now(),
        ]);

        Artisan::call('reports:generate-daily');

        $date = today()->format('Y-m-d');
        Storage::disk('local')->assertExists("reports/daily/{$date}/station-manager.csv");
        Storage::disk('local')->assertExists("reports/daily/{$date}/station-manager.pdf");
        $this->assertStringContainsString('Bonanjo', Storage::disk('local')->get("reports/daily/{$date}/station-manager.csv"));

        $director = User::factory()->create(['role' => User::ROLE_GENERAL_DIRECTOR]);

        $this->actingAs($director)
            ->get(route('admin.reports.index'))
            ->assertOk()
            ->assertSee('Archives quotidiennes')
            ->assertSee($date);

        $this->get(route('admin.reports.daily', ['date' => $date, 'format' => 'pdf']))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_stock_movements_are_tracked_independently_per_station(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $firstStation = Station::create([
            'name' => 'Bonanjo',
            'location' => 'Douala',
            'status' => 'active',
        ]);
        $secondStation = Station::create([
            'name' => 'Bastos',
            'location' => 'Yaoundé',
            'status' => 'active',
        ]);
        $product = Product::create([
            'name' => 'Super 95',
            'unit' => 'litre',
            'stock_quantity' => 500,
            'minimum_stock' => 25,
            'price' => 950,
        ]);

        $this->actingAs($admin)
            ->get('/admin/stocks')
            ->assertOk()
            ->assertSee('Saisir le stock d’ouverture');

        $this->post('/admin/stocks', [
            'station_id' => $firstStation->id,
            'product_id' => $product->id,
            'quantity' => 100,
        ])
            ->assertRedirect('/admin/stocks');

        $this->post('/admin/stocks', [
            'station_id' => $secondStation->id,
            'product_id' => $product->id,
            'quantity' => 200,
        ])->assertRedirect('/admin/stocks');

        $this->post('/admin/supplies', [
            'station_id' => $firstStation->id,
            'product_id' => $product->id,
            'quantity' => 40,
            'unit_price' => 800,
            'received_at' => now()->toDateTimeLocalString(),
        ])->assertRedirect('/admin/supplies');

        $this->post('/admin/sales', [
            'station_id' => $firstStation->id,
            'product_id' => $product->id,
            'quantity' => 30,
            'unit_price' => 950,
            'sold_at' => now()->toDateTimeLocalString(),
        ])->assertRedirect('/admin/sales');

        $this->assertDatabaseHas('station_stocks', [
            'station_id' => $firstStation->id,
            'product_id' => $product->id,
            'quantity' => 110,
        ]);
        $this->assertDatabaseHas('station_stocks', [
            'station_id' => $secondStation->id,
            'product_id' => $product->id,
            'quantity' => 200,
        ]);

        $this->from('/admin/sales')
            ->post('/admin/sales', [
                'station_id' => $secondStation->id,
                'product_id' => $product->id,
                'quantity' => 201,
                'unit_price' => 950,
                'sold_at' => now()->toDateTimeLocalString(),
            ])
            ->assertRedirect('/admin/sales')
            ->assertSessionHasErrors('quantity');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 500,
        ]);
    }
}
