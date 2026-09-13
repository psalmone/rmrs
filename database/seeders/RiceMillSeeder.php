<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Farmer;
use App\Models\Item;
use App\Models\PalayIntake;
use App\Models\MillingBatch;
use App\Models\MillingOutput;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use Illuminate\Support\Facades\Hash;

class RiceMillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create or get default Operator / Admin User
        $user = User::firstOrCreate(
            ['email' => 'admin@ricemill.com'],
            [
                'name' => 'MRMS Mill Administrator',
                'password' => Hash::make('password123'),
            ]
        );

        // 2. Seed Master Inventory Items
        $itemsData = [
            // Raw Palay
            [
                'code' => 'PALAY-INB-50',
                'name' => 'Raw Palay (Inbred Grain)',
                'category' => 'raw_palay',
                'variety' => 'Inbred',
                'unit' => 'sack_50kg',
                'current_stock' => 1240,
                'reorder_level' => 200,
                'unit_cost' => 1100.00,
                'unit_price' => 1150.00,
            ],
            [
                'code' => 'PALAY-HYB-50',
                'name' => 'Raw Palay (Hybrid / SL-8H)',
                'category' => 'raw_palay',
                'variety' => 'Hybrid',
                'unit' => 'sack_50kg',
                'current_stock' => 580,
                'reorder_level' => 100,
                'unit_cost' => 1250.00,
                'unit_price' => 1300.00,
            ],
            // Milled Rice Varieties
            [
                'code' => 'RICE-SIN-50',
                'name' => 'Sinandomeng Special (50kg)',
                'category' => 'milled_rice',
                'variety' => 'Sinandomeng',
                'unit' => 'sack_50kg',
                'current_stock' => 450,
                'reorder_level' => 50,
                'unit_cost' => 2250.00,
                'unit_price' => 2450.00,
            ],
            [
                'code' => 'RICE-DIN-50',
                'name' => 'Dinorado Aromatic (50kg)',
                'category' => 'milled_rice',
                'variety' => 'Dinorado',
                'unit' => 'sack_50kg',
                'current_stock' => 320,
                'reorder_level' => 40,
                'unit_cost' => 2400.00,
                'unit_price' => 2650.00,
            ],
            [
                'code' => 'RICE-WM-50',
                'name' => 'Well-Milled Rice (50kg)',
                'category' => 'milled_rice',
                'variety' => 'Well-Milled',
                'unit' => 'sack_50kg',
                'current_stock' => 610,
                'reorder_level' => 80,
                'unit_cost' => 2050.00,
                'unit_price' => 2200.00,
            ],
            [
                'code' => 'RICE-SIN-25',
                'name' => 'Sinandomeng Special (25kg Sack)',
                'category' => 'milled_rice',
                'variety' => 'Sinandomeng',
                'unit' => 'sack_25kg',
                'current_stock' => 280,
                'reorder_level' => 30,
                'unit_cost' => 1150.00,
                'unit_price' => 1280.00,
            ],
            // By-Products
            [
                'code' => 'BY-DARAK-D1',
                'name' => 'Rice Bran D1 (Darak Class 1)',
                'category' => 'by_product',
                'variety' => 'Darak D1',
                'unit' => 'sack_50kg',
                'current_stock' => 185,
                'reorder_level' => 30,
                'unit_cost' => 450.00,
                'unit_price' => 550.00,
            ],
            [
                'code' => 'BY-BINLID',
                'name' => 'Broken Rice (Binlid / Brewers)',
                'category' => 'by_product',
                'variety' => 'Binlid',
                'unit' => 'sack_50kg',
                'current_stock' => 95,
                'reorder_level' => 20,
                'unit_cost' => 900.00,
                'unit_price' => 1100.00,
            ],
            [
                'code' => 'BY-IPA-HULL',
                'name' => 'Rice Hull / Husk (Ipa Bulk)',
                'category' => 'by_product',
                'variety' => 'Husk',
                'unit' => 'sack_50kg',
                'current_stock' => 240,
                'reorder_level' => 50,
                'unit_cost' => 30.00,
                'unit_price' => 60.00,
            ],
        ];

        $createdItems = [];
        foreach ($itemsData as $data) {
            $createdItems[$data['code']] = Item::updateOrCreate(
                ['code' => $data['code']],
                $data
            );
        }

        // 3. Seed Farmers / Palay Producers
        $farmer1 = Farmer::firstOrCreate(
            ['code' => 'FARM-001'],
            [
                'name' => 'Mang Danilo Santos',
                'phone' => '0917-555-1234',
                'address' => 'Barangay San Isidro, Rice Plains',
                'notes' => 'Regular supplier of dry Inbred palay, 5-hectare farm.',
            ]
        );

        $farmer2 = Farmer::firstOrCreate(
            ['code' => 'FARM-002'],
            [
                'name' => 'Eduardo Ramos Agricultural Coop',
                'phone' => '0928-333-8899',
                'address' => 'Sitio Maligaya, Valley South',
                'notes' => 'Cooperative providing hybrid variety SL-8H.',
            ]
        );

        // 4. Seed Palay Intake Tickets
        $intake1 = PalayIntake::firstOrCreate(
            ['ticket_number' => 'TKT-2026-0001'],
            [
                'farmer_id' => $farmer1->id,
                'farmer_name' => $farmer1->name,
                'variety_type' => 'Inbred',
                'bag_count' => 150,
                'gross_weight_kg' => 7650.00,
                'tare_weight_kg' => 150.00,
                'moisture_content_pct' => 14.20,
                'deduction_kg' => 75.00,
                'net_weight_kg' => 7425.00,
                'price_per_kg' => 23.50,
                'total_amount' => 174487.50,
                'payment_status' => 'paid',
                'received_by_user_id' => $user->id,
                'intake_date' => now()->subDays(2),
                'notes' => 'Clean grain, good moisture level.',
            ]
        );

        $intake2 = PalayIntake::firstOrCreate(
            ['ticket_number' => 'TKT-2026-0002'],
            [
                'farmer_id' => $farmer2->id,
                'farmer_name' => $farmer2->name,
                'variety_type' => 'Hybrid',
                'bag_count' => 220,
                'gross_weight_kg' => 11200.00,
                'tare_weight_kg' => 220.00,
                'moisture_content_pct' => 13.80,
                'deduction_kg' => 50.00,
                'net_weight_kg' => 10930.00,
                'price_per_kg' => 25.00,
                'total_amount' => 273250.00,
                'payment_status' => 'partial',
                'received_by_user_id' => $user->id,
                'intake_date' => now()->subDay(),
                'notes' => 'High quality harvest, 50% downpayment issued.',
            ]
        );

        // 5. Seed Milling Batches with Yield Recovery Calculations
        // Batch 1: Input 5,000 kg -> Output 3,425 kg Milled Rice (68.5% recovery)
        $batch1 = MillingBatch::firstOrCreate(
            ['batch_number' => 'BATCH-2026-001'],
            [
                'operator_user_id' => $user->id,
                'palay_intake_id' => $intake1->id,
                'palay_variety' => 'Inbred (Sinandomeng)',
                'input_sacks' => 100,
                'input_weight_kg' => 5000.00,
                'started_at' => now()->subHours(10),
                'completed_at' => now()->subHours(6),
                'status' => 'completed',
                'total_milled_output_kg' => 3425.00,
                'recovery_rate_pct' => 68.50, // 3425 / 5000 * 100
                'notes' => 'Standard mill pass. Excellent white head rice recovery.',
            ]
        );

        // Milling Outputs for Batch 1
        MillingOutput::firstOrCreate(
            [
                'milling_batch_id' => $batch1->id,
                'item_id' => $createdItems['RICE-SIN-50']->id,
            ],
            [
                'output_type' => 'milled_rice',
                'quantity_units' => 68.50, // sacks
                'weight_kg' => 3425.00,
            ]
        );

        MillingOutput::firstOrCreate(
            [
                'milling_batch_id' => $batch1->id,
                'item_id' => $createdItems['BY-DARAK-D1']->id,
            ],
            [
                'output_type' => 'darak_bran',
                'quantity_units' => 11.00,
                'weight_kg' => 550.00,
            ]
        );

        MillingOutput::firstOrCreate(
            [
                'milling_batch_id' => $batch1->id,
                'item_id' => $createdItems['BY-BINLID']->id,
            ],
            [
                'output_type' => 'binlid_broken',
                'quantity_units' => 4.50,
                'weight_kg' => 225.00,
            ]
        );

        // 6. Seed Customers (Wholesale & Retail)
        $customer1 = Customer::firstOrCreate(
            ['code' => 'CUST-001'],
            [
                'name' => 'Golden Grain Wholesalers Corp.',
                'customer_type' => 'wholesale',
                'phone' => '0919-876-5432',
                'address' => 'Metro Public Market Wholesale Block',
                'credit_limit' => 200000.00,
                'current_balance' => 45000.00,
            ]
        );

        $customer2 = Customer::firstOrCreate(
            ['code' => 'CUST-002'],
            [
                'name' => 'Nanay Lilia Rice Store (Retail)',
                'customer_type' => 'retail',
                'phone' => '0915-444-2211',
                'address' => 'Corner Poblacion Street',
                'credit_limit' => 30000.00,
                'current_balance' => 0.00,
            ]
        );

        // 7. Seed Sales Invoices & Line Items
        $sale1 = Sale::firstOrCreate(
            ['invoice_number' => 'INV-2026-0001'],
            [
                'customer_id' => $customer1->id,
                'customer_name' => $customer1->name,
                'sale_type' => 'wholesale',
                'total_amount' => 98000.00,
                'discount_amount' => 1000.00,
                'net_amount' => 97000.00,
                'paid_amount' => 52000.00,
                'balance_amount' => 45000.00,
                'payment_status' => 'partial',
                'payment_method' => 'bank_transfer',
                'cashier_user_id' => $user->id,
                'sale_date' => now()->subDay(),
                'notes' => '40 sacks Sinandomeng 50kg wholesale contract.',
            ]
        );

        SaleItem::firstOrCreate(
            [
                'sale_id' => $sale1->id,
                'item_id' => $createdItems['RICE-SIN-50']->id,
            ],
            [
                'item_name' => 'Sinandomeng Special (50kg)',
                'quantity' => 40,
                'unit_price' => 2450.00,
                'subtotal' => 98000.00,
            ]
        );

        Payment::firstOrCreate(
            ['sale_id' => $sale1->id, 'reference_number' => 'BDO-TRX-99881'],
            [
                'customer_id' => $customer1->id,
                'amount' => 52000.00,
                'payment_method' => 'bank_transfer',
                'payment_date' => now()->subDay(),
                'received_by_user_id' => $user->id,
                'notes' => 'Downpayment deposit confirmed via online bank.',
            ]
        );
    }
}
