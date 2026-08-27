<?php

namespace Database\Seeders;

use App\Enums\AdminRole;
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
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => AdminRole::SystemAdmin,
        ]);

        $this->call([
            SiteContentSeeder::class, // e-mail sablonok + alap site settings
            FugefaBaselineSeeder::class, // alap fejléc/lábléc
            FugefaPagesSeeder::class, // oldalépítő blokkok + lábléc, impresszum, adatkezelés, pályázat
            ExtendedDomainSeeder::class, // fizetési módok (inaktív stubok)
        ]);
    }
}
