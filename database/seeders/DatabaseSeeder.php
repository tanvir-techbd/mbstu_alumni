<?php

namespace Database\Seeders;

use App\Enums\DocumentCategory;
use App\Enums\GalleryCategory;
use App\Enums\RoleName;
use App\Models\AlumniProfile;
use App\Models\Document;
use App\Models\Donation;
use App\Models\DonationCampaign;
use App\Models\Event;
use App\Models\FeedbackReply;
use App\Models\FeedbackTicket;
use App\Models\Gallery;
use App\Models\JobPosting;
use App\Models\MentorshipRequest;
use App\Models\Notice;
use App\Models\SuccessStory;
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

        // Success Stories: only from *verified* alumni, same rule as Job Portal.
        $publishedStories = SuccessStory::factory(2)->published()->create(['user_id' => $approvedAlumni[0]->id]);
        $publishedStories->push(SuccessStory::factory()->published()->create(['user_id' => $approvedAlumni[1]->id]));
        SuccessStory::factory()->create(['user_id' => $approvedAlumni[2]->id]); // pending
        SuccessStory::factory()->rejected()->create(['user_id' => $approvedAlumni[3]->id]);

        $publishedStories->each(function (SuccessStory $story) {
            $story->images()->create([
                'image_path' => 'success-story-images/seed-'.$story->id.'.png',
            ]);
            Storage::disk('public')->put(
                'success-story-images/seed-'.$story->id.'.png',
                DummyAvatarGenerator::generate($story->title)
            );
        });

        // Donation campaigns + donations spread across the last 6 months, so the
        // admin dashboard's Monthly Donations chart has real data to render.
        $campaigns = DonationCampaign::factory(2)->create(['created_by' => $admin->id]);
        $campaigns->push(DonationCampaign::factory()->closed()->create(['created_by' => $admin->id]));

        $donors = User::role(RoleName::Alumni->value)->inRandomOrder()->take(6)->get()
            ->merge(User::role(RoleName::Student->value)->inRandomOrder()->take(4)->get())
            ->push($alumni)
            ->push($student)
            ->unique('id')
            ->values();

        foreach (range(0, 14) as $i) {
            $donation = Donation::factory()->make([
                'donation_campaign_id' => $campaigns->random()->id,
                'user_id' => $donors->random()->id,
                'donated_at' => now()->subMonths(rand(0, 5))->subDays(rand(0, 27)),
            ]);
            $donation->receipt_number = 'PENDING';
            $donation->save();
            $donation->receipt_number = 'MBSTU-DON-'.str_pad((string) $donation->id, 6, '0', STR_PAD_LEFT);
            $donation->save();
        }

        // Gallery: one album per category, a handful of placeholder photos each.
        foreach (GalleryCategory::cases() as $category) {
            $gallery = Gallery::factory()->create([
                'category' => $category->value,
                'created_by' => fake()->boolean() ? $admin->id : $faculty->id,
            ]);

            foreach (range(1, rand(3, 6)) as $i) {
                $path = 'gallery-images/seed-'.$gallery->id.'-'.$i.'.png';
                Storage::disk('public')->put($path, DummyAvatarGenerator::generate($gallery->title.' '.$i));
                $gallery->images()->create(['image_path' => $path]);
            }
        }

        // Documents: one of each category from admin, plus a couple from faculty,
        // each with a real (dummy) file on the private disk so downloads work.
        foreach (DocumentCategory::cases() as $category) {
            $document = Document::factory()->create([
                'category' => $category->value,
                'uploaded_by' => $admin->id,
                'file_path' => 'PENDING',
            ]);
            $path = 'documents/seed-'.$document->id.'.pdf';
            Storage::disk('local')->put($path, $dummyPdf);
            $document->file_path = $path;
            $document->file_size = Storage::disk('local')->size($path);
            $document->save();
        }

        $facultyDocument = Document::factory()->create([
            'category' => DocumentCategory::Forms->value,
            'title' => 'Alumni Association Membership Form',
            'uploaded_by' => $faculty->id,
            'file_path' => 'PENDING',
        ]);
        $facultyPath = 'documents/seed-'.$facultyDocument->id.'.pdf';
        Storage::disk('local')->put($facultyPath, $dummyPdf);
        $facultyDocument->file_path = $facultyPath;
        $facultyDocument->file_size = Storage::disk('local')->size($facultyPath);
        $facultyDocument->save();

        // Feedback: a mix of open/closed tickets from different roles, a couple
        // with an admin reply already in the thread.
        $openTicket = FeedbackTicket::factory()->create(['user_id' => $alumni->id]);
        $this->addReply($openTicket, $admin, 'Thanks for the suggestion, we\'re looking into it.');

        FeedbackTicket::factory()->create(['user_id' => $student->id]);

        $closedTicket = FeedbackTicket::factory()->closed()->create(['user_id' => $faculty->id]);
        $this->addReply($closedTicket, $admin, 'Resolved — please let us know if the issue comes back.');

        FeedbackTicket::factory(4)->create([
            'user_id' => fn () => User::role(RoleName::Alumni->value)->inRandomOrder()->first()->id,
        ]);
        FeedbackTicket::factory(2)->closed()->create([
            'user_id' => fn () => User::role(RoleName::Student->value)->inRandomOrder()->first()->id,
        ]);
    }

    /**
     * Direct-property-assignment (not a fillable-array create()) so the FKs
     * on FeedbackReply, which are deliberately excluded from $fillable, land
     * correctly — same pattern used for every posted_by/created_by FK since M8.
     */
    private function addReply(FeedbackTicket $ticket, User $replier, string $message): void
    {
        $reply = new FeedbackReply(['message' => $message]);
        $reply->feedback_ticket_id = $ticket->id;
        $reply->user_id = $replier->id;
        $reply->save();
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
