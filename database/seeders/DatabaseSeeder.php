<?php

namespace Database\Seeders;

use App\Enums\RoleName;
use App\Models\AlumniProfile;
use App\Models\Event;
use App\Models\JobPosting;
use App\Models\MentorshipRequest;
use App\Models\Notice;
use App\Models\User;
use Database\Support\DummyAvatarGenerator;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
        $this->attachDummyPhoto($alumni);

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
        $bulkAlumni = User::factory(12)->create()->each(function (User $user) {
            $user->assignRole(RoleName::Alumni->value);
            $this->attachDummyPhoto($user);
        });
        $bulkAlumni->slice(0, 6)->each(fn (User $user) => AlumniProfile::factory()->for($user)->create());
        $approvedAlumni = $bulkAlumni->slice(6, 4)->values();
        $approvedAlumni->each(fn (User $user) => AlumniProfile::factory()->approved()->for($user)->create(['reviewed_by' => $admin->id]));
        $bulkAlumni->slice(10, 2)->each(fn (User $user) => AlumniProfile::factory()->rejected()->for($user)->create(['reviewed_by' => $admin->id]));

        User::factory(8)->create()->each(fn (User $user) => $user->assignRole(RoleName::Student->value));
        User::factory(5)->create()->each(fn (User $user) => $user->assignRole(RoleName::Faculty->value));

        User::factory(4)->create(['status' => User::STATUS_INACTIVE])
            ->each(function (User $user) {
                $user->assignRole(RoleName::Alumni->value);
                AlumniProfile::factory()->for($user)->create();
                $this->attachDummyPhoto($user);
            });

        // Events: a mix of statuses and creators for the M5 module.
        $publishedEvents = Event::factory(3)->published()->create(['created_by' => $faculty->id]);
        $publishedEvents->push(Event::factory()->published()->create(['created_by' => $admin->id]));
        Event::factory(2)->create(['created_by' => $faculty->id]); // drafts
        Event::factory(2)->archived()->create(['created_by' => $admin->id]);

        $registrants = User::role(RoleName::Alumni->value)->inRandomOrder()->take(5)->get()
            ->merge(User::role(RoleName::Student->value)->inRandomOrder()->take(3)->get())
            ->push($student);

        $publishedEvents->first()->registrations()->createMany(
            $registrants->unique('id')->map(fn (User $user) => [
                'user_id' => $user->id,
                'attended' => fake()->boolean(30),
            ])->values()->all()
        );

        // Job postings: only from *verified* alumni, per the module's authorization rule.
        $publishedJobs = JobPosting::factory(4)->published()->create(['posted_by' => $approvedAlumni->random()->id]);
        JobPosting::factory(2)->create(['posted_by' => $approvedAlumni->first()->id]); // pending
        JobPosting::factory()->rejected()->create(['posted_by' => $approvedAlumni->last()->id]);

        $bookmarkers = User::role(RoleName::Student->value)->inRandomOrder()->take(4)->get()->push($student)->unique('id');
        $publishedJobs->take(2)->each(function (JobPosting $job) use ($bookmarkers) {
            $job->bookmarkedBy()->attach($bookmarkers->random(min(3, $bookmarkers->count()))->pluck('id'));
        });

        // Mentorship: students requesting verified alumni as mentors, mixed statuses.
        $otherStudents = User::role(RoleName::Student->value)->inRandomOrder()->take(3)->get();

        MentorshipRequest::factory()->create(['student_id' => $student->id, 'mentor_id' => $approvedAlumni[0]->id]);
        MentorshipRequest::factory()->accepted()->create(['student_id' => $otherStudents[0]->id, 'mentor_id' => $approvedAlumni[0]->id]);
        MentorshipRequest::factory()->rejected()->create(['student_id' => $otherStudents[1]->id, 'mentor_id' => $approvedAlumni[1]->id]);
        MentorshipRequest::factory()->completed()->create(['student_id' => $otherStudents[2]->id, 'mentor_id' => $approvedAlumni[2]->id]);

        // Notice Board: posted by admin and faculty, a couple with a real downloadable attachment.
        $notices = Notice::factory(4)->create(['posted_by' => $admin->id]);
        $notices = $notices->merge(Notice::factory(3)->create(['posted_by' => $faculty->id]));

        $dummyPdf = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF";
        $notices->take(2)->each(function (Notice $notice) use ($dummyPdf) {
            $path = 'notice-attachments/'.$notice->id.'.pdf';
            Storage::disk('public')->put($path, $dummyPdf);
            $notice->update(['attachment_path' => $path]);
        });

        $notices->first()->bookmarkedBy()->attach($student->id);
    }

    /**
     * Generate a placeholder avatar (initials over a colored background) so
     * seeded alumni have a photo in the Directory instead of a blank fallback.
     */
    private function attachDummyPhoto(User $user): void
    {
        $path = 'profile-photos/'.$user->id.'.png';

        Storage::disk('public')->put($path, DummyAvatarGenerator::generate($user->name));

        $user->update(['profile_photo_path' => $path]);
    }
}
