<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = storage_path('app/private/csv_data/property-data.csv');

        if (!is_file($path)) {
            $this->command?->warn("CSV data not found at {$path}.");
            return;
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            $this->command?->warn('Unable to open CSV file for reading.');
            return;
        }

        $header = fgetcsv($handle);

        if ($header === false) {
            fclose($handle);
            $this->command?->warn('CSV file appears to be empty.');
            return;
        }

        $rows = [];

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) < 6) {
                continue;
            }

            [$name, $price, $bedrooms, $bathrooms, $storeys, $garages] = $data;

            $rows[] = [
                'name' => trim($name),
                'price' => (int) $price,
                'bedrooms' => (int) $bedrooms,
                'bathrooms' => (int) $bathrooms,
                'storeys' => (int) $storeys,
                'garages' => (int) $garages,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($rows) >= 200) {
                Property::insert($rows);
                $rows = [];
            }
        }

        if ($rows !== []) {
            Property::insert($rows);
        }

        fclose($handle);
    }
}
