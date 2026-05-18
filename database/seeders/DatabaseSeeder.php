<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\StockItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin User ──
        User::create([
            'name'     => 'System Administrator',
            'email'    => 'admin@hospital.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // ── Doctors ──
        $doctors = [
            ['name' => 'Dr. James Mwangi',    'email' => 'james@hospital.com',    'specialization' => 'General Medicine'],
            ['name' => 'Dr. Amina Osei',      'email' => 'amina@hospital.com',     'specialization' => 'Pediatrics'],
            ['name' => 'Dr. Robert Kiprotich','email' => 'robert@hospital.com',    'specialization' => 'Surgery'],
            ['name' => 'Dr. Grace Ndungu',    'email' => 'grace@hospital.com',     'specialization' => 'Obstetrics'],
        ];

        foreach ($doctors as $d) {
            User::create(array_merge($d, [
                'password' => Hash::make('password'),
                'role'     => 'doctor',
            ]));
        }

        $doctorIds = User::where('role', 'doctor')->pluck('id');

        // ── Patients ──
        $wards    = ['General', 'ICU', 'Pediatric', 'Maternity', 'Surgical', 'Emergency'];
        $statuses = ['admitted', 'stable', 'critical', 'observation', 'recovering'];

        $patients_data = [
            ['name' => 'Mary Wanjiku',      'gender' => 'female', 'ward' => 'General',   'status' => 'stable',      'diagnosis' => 'Malaria'],
            ['name' => 'John Odhiambo',     'gender' => 'male',   'ward' => 'ICU',        'status' => 'critical',    'diagnosis' => 'Severe pneumonia'],
            ['name' => 'Fatuma Abdi',        'gender' => 'female', 'ward' => 'Maternity',  'status' => 'admitted',    'diagnosis' => 'Labour'],
            ['name' => 'Peter Kamau',        'gender' => 'male',   'ward' => 'Surgical',   'status' => 'recovering',  'diagnosis' => 'Appendectomy'],
            ['name' => 'Alice Mutua',        'gender' => 'female', 'ward' => 'General',    'status' => 'observation', 'diagnosis' => 'Typhoid fever'],
            ['name' => 'Samuel Otieno',      'gender' => 'male',   'ward' => 'General',    'status' => 'stable',      'diagnosis' => 'Hypertension'],
            ['name' => 'Zainab Hassan',      'gender' => 'female', 'ward' => 'Pediatric',  'status' => 'stable',      'diagnosis' => 'Respiratory infection'],
            ['name' => 'David Njoroge',      'gender' => 'male',   'ward' => 'ICU',        'status' => 'critical',    'diagnosis' => 'Diabetic ketoacidosis'],
            ['name' => 'Esther Cherop',      'gender' => 'female', 'ward' => 'Surgical',   'status' => 'recovering',  'diagnosis' => 'Cholecystectomy'],
            ['name' => 'Hassan Juma',        'gender' => 'male',   'ward' => 'Emergency',  'status' => 'admitted',    'diagnosis' => 'Trauma - RTA'],
        ];

        foreach ($patients_data as $i => $pd) {
            Patient::create(array_merge($pd, [
                'date_of_birth'  => Carbon::now()->subYears(rand(18, 75)),
                'phone'          => '+255 7' . rand(10, 99) . ' ' . rand(100, 999) . ' ' . rand(100, 999),
                'doctor_id'      => $doctorIds->random(),
                'bed_number'     => strtoupper(substr($pd['ward'], 0, 1)) . '-' . ($i + 1),
                'admitted_at'    => Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 12)),
                'blood_pressure' => rand(110, 160) . '/' . rand(70, 100),
                'pulse'          => rand(60, 110),
                'temperature'    => rand(365, 395) / 10,
                'weight'         => rand(45, 95),
                'notes'          => 'Patient presented with ' . strtolower($pd['diagnosis']) . '. Monitoring and treatment initiated.',
            ]));
        }

        // One discharged patient
        Patient::create([
            'name'               => 'Grace Achieng',
            'gender'             => 'female',
            'date_of_birth'      => Carbon::now()->subYears(32),
            'ward'               => 'General',
            'status'             => 'discharged',
            'diagnosis'          => 'Gastroenteritis',
            'doctor_id'          => $doctorIds->first(),
            'admitted_at'        => Carbon::now()->subDays(4),
            'discharged_at'      => Carbon::now()->subHours(2),
            'discharge_condition'=> 'recovered',
            'discharge_notes'    => 'Fully recovered. Advised to maintain hydration. No follow-up required.',
        ]);

        // ── Stock Items ──
        $stock = [
            ['name' => 'Paracetamol 500mg',     'category' => 'medicine',    'quantity' => 500,  'unit' => 'tablets', 'reorder_level' => 100, 'expiry_date' => '2026-12-31'],
            ['name' => 'Amoxicillin 250mg',      'category' => 'medicine',    'quantity' => 8,    'unit' => 'tablets', 'reorder_level' => 50,  'expiry_date' => '2026-06-30'],
            ['name' => 'IV Normal Saline 500ml', 'category' => 'medicine',    'quantity' => 45,   'unit' => 'bottles', 'reorder_level' => 20,  'expiry_date' => '2025-08-31'],
            ['name' => 'Surgical Gloves (M)',    'category' => 'consumable',  'quantity' => 200,  'unit' => 'pairs',   'reorder_level' => 50,  'expiry_date' => null],
            ['name' => 'Syringes 5ml',           'category' => 'consumable',  'quantity' => 12,   'unit' => 'pieces',  'reorder_level' => 50,  'expiry_date' => null],
            ['name' => 'Metformin 500mg',        'category' => 'medicine',    'quantity' => 300,  'unit' => 'tablets', 'reorder_level' => 60,  'expiry_date' => '2026-09-30'],
            ['name' => 'Morphine 10mg/ml',       'category' => 'medicine',    'quantity' => 6,    'unit' => 'vials',   'reorder_level' => 10,  'expiry_date' => '2025-07-15'],
            ['name' => 'Oxygen Mask Adult',      'category' => 'consumable',  'quantity' => 30,   'unit' => 'pieces',  'reorder_level' => 10,  'expiry_date' => null],
            ['name' => 'Wound Dressing 10x10',   'category' => 'consumable',  'quantity' => 150,  'unit' => 'pieces',  'reorder_level' => 30,  'expiry_date' => null],
            ['name' => 'Artemether-Lumefantrine','category' => 'medicine',    'quantity' => 60,   'unit' => 'tablets', 'reorder_level' => 40,  'expiry_date' => '2026-11-30'],
        ];

        foreach ($stock as $s) {
            StockItem::create($s);
        }
    }
}
