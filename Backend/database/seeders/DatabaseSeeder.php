<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ShopSetting;
use App\Models\BankAccount;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Users
        // Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@corndogalle.com'],
            [
                'name' => 'Admin Alle',
                'password' => Hash::make('password123'),
                'role' => 'admin',
            ]
        );

        // Regular Customer
        $customer = User::updateOrCreate(
            ['email' => 'user@corndogalle.com'],
            [
                'name' => 'Rudyansyah',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ]
        );

        // Another customer
        $customer2 = User::updateOrCreate(
            ['email' => 'rena.karlina25@gmail.com'],
            [
                'name' => 'Rena Karlina',
                'password' => Hash::make('password123'),
                'role' => 'user',
            ]
        );

        // 2. Seed Shop Settings
        ShopSetting::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Corndog Alle',
                'address' => 'Jl. Cilandak, No 45, Jakarta Barat, DKI Jakarta 94107',
                'phone' => '(+62) 895-3653-05896',
                'email' => 'info@corndogalle.com',
            ]
        );

        // 3. Seed Bank Account
        BankAccount::updateOrCreate(
            ['id' => 1],
            [
                'bank_name' => 'BCA',
                'account_name' => 'Rena Karlina',
                'account_number' => '8889767817',
            ]
        );

        // 4. Load legacy db.json data
        $jsonPath = base_path('../db.json');
        if (!file_exists($jsonPath)) {
            $this->command->error("db.json not found at: {$jsonPath}");
            return;
        }

        $json = file_get_contents($jsonPath);
        $data = json_decode($json, true);

        // Seed Categories
        if (isset($data['categories'])) {
            foreach ($data['categories'] as $cat) {
                Category::updateOrCreate(
                    ['id' => $cat['id']],
                    ['name' => $cat['name']]
                );
            }
        }

        // Seed Products
        if (isset($data['products'])) {
            foreach ($data['products'] as $prod) {
                $legacyCatName = $prod['category']['name'] ?? 'food';
                $catName = ucfirst(strtolower($legacyCatName));
                
                $category = Category::where('name', 'like', $catName)->first();
                $categoryId = $category ? $category->id : 1;

                Product::updateOrCreate(
                    ['id' => $prod['id']],
                    [
                        'name' => $prod['name'],
                        'price' => $prod['price'],
                        'is_ready' => $prod['is_ready'] ?? true,
                        'img' => $prod['img'],
                        'category_id' => $categoryId,
                    ]
                );
            }
        }

        // Add additional product for the invoice mockup (Tteokbokki)
        $foodCategory = Category::where('name', 'Food')->first() ?? Category::first();
        $tteokbokki = Product::updateOrCreate(
            ['name' => 'Tteokbokki Extra Mozarella'],
            [
                'price' => 28000,
                'is_ready' => true,
                'img' => 'moza.jpg', // reusable image
                'category_id' => $foodCategory ? $foodCategory->id : 1,
            ]
        );

        // 5. Seed Mock Orders
        // Order 1: Rena Karlina (Invoice 181100)
        $order1 = Order::create([
            'user_id' => $customer2->id,
            'customer_name' => 'Rena Karlina',
            'table_number' => 'Meja 3',
            'first_name' => 'Rena',
            'last_name' => 'Karlina',
            'phone' => '(+62) 895-3653-05896',
            'email' => 'rena.karlina25@gmail.com',
            'address' => 'Jl. Cilandak Barat No. 45',
            'alt_address' => null,
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Barat',
            'postal_code' => '171231',
            'invoice_number' => '181100',
            'payment_method' => 'BCA Virtual Account',
            'payment_status' => 'paid',
            'shipping_cost' => 13000,
            'total_price' => 79000, // 66000 + 13000
            'status' => 'Perlu dikirim', // matches screen status
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => 2, // Corndog Hotang
            'quantity' => 2,
            'price' => 17000,
            'notes' => null,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => 3, // Corndog Choco Chips
            'quantity' => 1,
            'price' => 15000,
            'notes' => null,
        ]);

        OrderItem::create([
            'order_id' => $order1->id,
            'product_id' => 1, // Corndog Mozarella
            'quantity' => 1,
            'price' => 17000,
            'notes' => null,
        ]);

        // Order 2: Rudyansyah (Invoice 181166)
        $order2 = Order::create([
            'user_id' => $customer->id,
            'customer_name' => 'Rudyansyah',
            'table_number' => 'Meja 5',
            'first_name' => 'Rudy',
            'last_name' => 'ansyah',
            'phone' => '(+62) 539-1037-7331',
            'email' => 'rudyansyah16@gmail.com',
            'address' => 'Jl. Sudirman No. 32',
            'alt_address' => null,
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta Pusat',
            'postal_code' => '616412',
            'invoice_number' => '181166',
            'payment_method' => 'Cash on Delivery',
            'payment_status' => 'unpaid',
            'shipping_cost' => 17000,
            'total_price' => 116000, // 99000 + 17000
            'status' => 'Perlu di Cek', // matches screen status
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => 8, // Combo Hemat
            'quantity' => 5,
            'price' => 15000,
            'notes' => null,
        ]);

        OrderItem::create([
            'order_id' => $order2->id,
            'product_id' => 4, // Coca Cola
            'quantity' => 2,
            'price' => 12000,
            'notes' => null,
        ]);

        // Order 3: Rudyansyah (Invoice 007612)
        $order3 = Order::create([
            'user_id' => $customer->id,
            'customer_name' => 'Rudyansyah',
            'table_number' => 'Meja 1',
            'first_name' => 'Rudy',
            'last_name' => 'ansyah',
            'phone' => '(+62) 539-1037-7331',
            'email' => 'rudyansyah14@gmail.com',
            'address' => 'Jl. Sindang Barang No 22, Kecamatan Bogor Barat',
            'alt_address' => null,
            'province' => 'Jawa Barat',
            'city' => 'Kota Bogor',
            'postal_code' => '16117',
            'invoice_number' => '007612',
            'payment_method' => 'BCA Virtual Account',
            'payment_status' => 'unpaid',
            'shipping_cost' => 10000,
            'total_price' => 94000, // 84000 + 10000
            'status' => 'Perlu di Cek',
        ]);

        OrderItem::create([
            'order_id' => $order3->id,
            'product_id' => $tteokbokki->id,
            'quantity' => 3,
            'price' => 28000,
            'notes' => '2 Mangkok pedas 1 sedang',
        ]);

        $this->command->info("Seeding completed successfully.");
    }
}
