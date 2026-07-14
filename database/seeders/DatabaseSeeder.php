<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\AlumniProfile;
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
        AlumniProfile::factory()->for($alumni)->create();

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

        // Bulk test data for the User Management screen (search/filter/pagination)
        // and the Alumni Verification screen (mixed pending/approved/rejected profiles).
        $bulkAlumni = User::factory(12)->create()->each(fn (User $user) => $user->assignRole(RoleName::Alumni->value));
        $bulkAlumni->slice(0, 6)->each(fn (User $user) => AlumniProfile::factory()->for($user)->create());
        $bulkAlumni->slice(6, 4)->each(fn (User $user) => AlumniProfile::factory()->approved()->for($user)->create(['reviewed_by' => $admin->id]));
        $bulkAlumni->slice(10, 2)->each(fn (User $user) => AlumniProfile::factory()->rejected()->for($user)->create(['reviewed_by' => $admin->id]));

        User::factory(8)->create()->each(fn (User $user) => $user->assignRole(RoleName::Student->value));
        User::factory(5)->create()->each(fn (User $user) => $user->assignRole(RoleName::Faculty->value));

        User::factory(4)->create(['status' => User::STATUS_INACTIVE])
            ->each(function (User $user) {
                $user->assignRole(RoleName::Alumni->value);
                AlumniProfile::factory()->for($user)->create();
            });
    }
}
