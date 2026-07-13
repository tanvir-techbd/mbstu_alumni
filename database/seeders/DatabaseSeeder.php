<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $admin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@mbstu-alumni.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $admin->assignRole(RoleName::SuperAdmin->value);

        $alumni = User::factory()->create([
            'name' => 'Demo Alumni',
            'email' => 'alumni@mbstu-alumni.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $alumni->assignRole(RoleName::Alumni->value);

        $student = User::factory()->create([
            'name' => 'Demo Student',
            'email' => 'student@mbstu-alumni.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $student->assignRole(RoleName::Student->value);

        $faculty = User::factory()->create([
            'name' => 'Demo Faculty',
            'email' => 'faculty@mbstu-alumni.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        $faculty->assignRole(RoleName::Faculty->value);

        // Bulk test data for the User Management screen (search/filter/pagination).
        User::factory(12)->create()->each(fn (User $user) => $user->assignRole(RoleName::Alumni->value));
        User::factory(8)->create()->each(fn (User $user) => $user->assignRole(RoleName::Student->value));
        User::factory(5)->create()->each(fn (User $user) => $user->assignRole(RoleName::Faculty->value));
        User::factory(4)->create(['status' => User::STATUS_INACTIVE])
            ->each(fn (User $user) => $user->assignRole(RoleName::Alumni->value));
    }
}
