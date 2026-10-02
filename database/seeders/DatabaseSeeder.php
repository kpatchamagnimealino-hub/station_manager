<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Station;
use App\Models\StationStock;
use App\Models\Supply;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Administrateur',
            'email' => 'admin@stationmanager.test',
            'password' => bcrypt('password'),
            'is_admin' => true,
            'role' => User::ROLE_ADMINISTRATOR,
        ]);

        User::factory()->create([
            'name' => 'Responsable de station',
            'email' => 'responsable@stationmanager.test',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'role' => User::ROLE_STATION_MANAGER,
        ]);

        User::factory()->create([
            'name' => 'Directeur général',
            'email' => 'directeur@stationmanager.test',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'role' => User::ROLE_GENERAL_DIRECTOR,
        ]);

        $stationA = Station::create([
            'name' => 'Bonanjo',
            'location' => 'Douala',
            'status' => 'active',
        ]);

        $stationB = Station::create([
            'name' => 'Bastos',
            'location' => 'Yaoundé',
            'status' => 'active',
        ]);

        $super = Product::create([
            'name' => 'Super 95',
            'unit' => 'litre',
            'stock_quantity' => 0,
            'minimum_stock' => 4000,
            'price' => 980,
            'description' => 'Essence super 95',
        ]);

        $gasoil = Product::create([
            'name' => 'Gasoil',
            'unit' => 'litre',
            'stock_quantity' => 0,
            'minimum_stock' => 3000,
            'price' => 860,
            'description' => 'Carburant diesel',
        ]);

        Supply::create([
            'station_id' => $stationA->id,
            'product_id' => $super->id,
            'quantity' => 5000,
            'unit_price' => 900,
            'received_at' => now()->subDay(),
        ]);

        Supply::create([
            'station_id' => $stationB->id,
            'product_id' => $gasoil->id,
            'quantity' => 4000,
            'unit_price' => 820,
            'received_at' => now()->subHours(6),
        ]);

        Sale::create([
            'station_id' => $stationA->id,
            'product_id' => $super->id,
            'quantity' => 1500,
            'unit_price' => 980,
            'sold_at' => now()->subHours(2),
        ]);

        Sale::create([
            'station_id' => $stationB->id,
            'product_id' => $gasoil->id,
            'quantity' => 800,
            'unit_price' => 860,
            'sold_at' => now()->subHours(3),
        ]);

        StationStock::create([
            'station_id' => $stationA->id,
            'product_id' => $super->id,
            'quantity' => 3500,
        ]);

        StationStock::create([
            'station_id' => $stationB->id,
            'product_id' => $gasoil->id,
            'quantity' => 3200,
        ]);
    }
}
