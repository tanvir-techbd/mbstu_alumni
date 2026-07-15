# Plan

Roadmap + living progress log. For *what*/*why* (requirements, schema, architecture), see `DESIGN.md`.

---

## Roadmap

Suggested build order — dependency-driven (Directory needs Profiles; Notifications/Reports/Activity Log are easiest to wire in once there are real events to hook into). Adjust freely; tick the checkbox and add an entry to the progress log below when a milestone lands.

- [x] **M0 — Project scaffolding.** Laravel 12 + Breeze (blade, dark mode) + Spatie Permission + dompdf + Laravel Excel installed. Base roles seeded (`super-admin`, `alumni`, `student`, `faculty`). `users` table extended with `phone`, `status`, `profile_photo_path`. Dark/light mode toggle wired (Alpine store + no-FOUC script). `static_prototype_folder/` and `.claude/` set up.
- [x] **M1 — Role-based dashboard shell.** Sticky sidebar + topbar layout (replaces Breeze's simple top nav), role-aware sidebar nav, breadcrumbs, `DashboardController` dispatches to one view per role with stat-card placeholders (real counts where `users`/roles data already exists, honest "Available after M-x" placeholders elsewhere).
- [x] **M2 — User Management.** Admin CRUD on users under `/admin/users`, role assignment, activate/deactivate, search/filter/paginate, delete guarded against self-delete and deleting the last super-admin.
- [x] **M3 — Alumni Profile + Verification workflow.** `alumni_profiles` table (1:1 `users`), self-service profile form + photo/document upload, admin review queue with approve/reject, registration now assigns a role and auto-creates the profile.
- [x] **M4 — Alumni Directory.** Search/sort over verified alumni, reachable by any authenticated role — no new table.
- [x] **M5 — Events.** Admin+faculty CRUD/publish/archive (first module with two management roles), registration with capacity/deadline, Excel participant export, attendance marking.
- [x] **M6 — Job Portal.** Verified-alumni-only posting → pending approval → admin publish/reject workflow, browse/search/bookmark for everyone. Table named `job_postings` to avoid colliding with Laravel's own `jobs` (queue) table.
- [x] **M7 — Mentorship.** Student requests a verified alumni mentor (via a "Request Mentorship" button on the Directory profile page, not a separate browse UI) → accept/reject → schedule meeting → completed, or student withdraws while pending.
- [x] **M8 — Notice Board.** Notice/circular/scholarship/news/announcement by admin+faculty, search, attachment download, bookmarks. Deliberately no draft/publish workflow — the brief doesn't ask for one here, unlike Events.
- [x] **M9 — Success Stories.** Verified-alumni-only submission (same rule as Job Portal) → pending → admin approve/reject → published, with a multi-image gallery per story.
- [x] **M10 — Donations.** Campaigns (admin-only), donate (record-based, no payment gateway), history, PDF receipts, admin reports. Also installed Chart.js and wired both admin dashboard charts; fixed an M6 gap (admin's "Jobs" card was never actually connected).
- [x] **M11 — Gallery.** Admin+faculty album management (Notice Board shape, not Events/Jobs — no visibility scoping needed), multi-photo albums with lightbox preview and native lazy loading.
- [x] **M12 — Documents.** Categorized repository, secure download.
- [x] **M13 — Feedback.** Suggestions/complaints/feature requests, admin reply/close, export.
- [ ] **M14 — Notifications.** In-app + email, wired into triggers from M3/M5/M6/M7/M10.
- [ ] **M15 — Reports.** PDF/Excel export for alumni list, events, jobs, donations, verification status.
- [ ] **M16 — Activity Log.** Login/logout, profile update, job creation, event registration, donation, approval actions.
- [ ] **M17 — Global Search.** Alumni, jobs, events, notices, documents.
- [ ] **M18 — Final polish.** Toasts, loading skeletons, empty states, confirmation dialogs, responsive + dark/light QA pass, rate-limiting/security review across all modules.

### Per-milestone checklist (apply to every module milestone)

1. Explain architecture + DB schema *before* writing code
2. Migration files
3. Models + relationships
4. Policy (authorization)
5. Form Request(s) (validation)
6. Controller (resource, thin — logic in a Service class)
7. Routes
8. Blade views (built from a `static_prototype_folder/` mockup where UI is non-trivial)
9. Seeder + Factory
10. Manual test pass (the `/verify` skill or equivalent — drive the actual flow, don't just eyeball the code)

---

## Progress log

Newest entry first. One entry per milestone/session — what shipped, what's next, anything surprising. Read this before re-deriving context from scratch.

### 2026-07-15 — M13: Feedback

**Done**
- `feedback_tickets` (`user_id` nullable + `nullOnDelete` — submitter, `type` enum-cast `suggestion`/`complaint`/`feature-request`, `subject`, `message`, `status` enum-cast `open`/`closed` default `open`, nullable `closed_at`) + `feedback_replies` (`feedback_ticket_id` `cascadeOnDelete`, `user_id` nullable + `nullOnDelete` — replier, `message`).
- **Structure decision, distinct from every module since M8**: this is a submit-then-manage workflow like Alumni Verification (M3), not a peer-content module — so it did *not* get the "faculty/creator manages own content" treatment used by Notice/Gallery/Documents. `FeedbackTicketPolicy`: `create` is open to all four roles, `view`/`reply` require being the ticket's owner or `super-admin`, `close`/`export` are `super-admin`-only. Tickets support a real back-and-forth thread — the owner can reply to their own ticket, not just receive a single admin response — but only admin can close it.
- **No `edit` route** — same precedent as Job Postings/Success Stories/Documents: once submitted, a ticket flows through state (open → closed) rather than being edited.
- Reply-blocked-when-closed is enforced as a **state check in `StoreFeedbackReplyRequest::withValidator()`**, not a Policy rule — kept separate from the identity check in `FeedbackTicketPolicy::reply()`, matching the state-vs-identity separation used throughout (e.g. Job Posting/Success Story approval flows never conflate "who" with "what state").
- `index` is dual-purpose in one controller/query: `super-admin` sees every ticket (search/type/status filterable), everyone else sees only their own — no separate admin route, no second controller.
- `FeedbackTicketsExport` (`FromCollection`/`WithHeadings`) — same shape as M5's `EventParticipantsExport`, admin-only route ordered before the `{ticket}` wildcard.
- Sidebar: the single (not four-role-duplicated, unlike Documents) "Feedback" placeholder lives in the shared "Account" block outside any `@role` conditional — correct, since submission is open to every role — wired to the real route.
- `FeedbackTicketFactory` (+ `closed()` state) and seeder: 9 tickets across alumni/student/faculty submitters (6 open, 3 closed), 2 with a real admin reply already in the thread, replies inserted via direct property assignment (not a fillable-array `create()`) for `feedback_ticket_id`/`user_id`, consistent with the FK pattern held clean since M8.

**Verified — full HTTP click-through against the real LAMPP-backed DB**:
- Admin: index shows all 9 tickets; can open any ticket regardless of owner; export returns a genuine `.xlsx` (`Microsoft Excel 2007+` via `file`, and the newly-created test ticket's subject was found in `xl/sharedStrings.xml` — not just a header check).
- Alumni: index scoped to exactly their own ticket; blocked (403, with a real CSRF token — distinguished from the CSRF-419 false positive an unauthenticated-token POST gives) from viewing another user's ticket, from `/feedback/export`, and from closing a ticket.
- Full thread lifecycle exercised end-to-end: alumni submitted a new ticket → replied to their own open ticket → admin replied → admin closed it → confirmed in DB (`status = closed`, exactly 2 replies) → alumni's further reply attempt correctly added **no** third reply row (validator's state check held), and the show page correctly fell back to "This ticket is closed" instead of a broken/empty reply form.
- Unauthenticated request to `/feedback` redirected (302).
- Reset to `migrate:fresh --seed` afterward; confirmed 9 tickets in the clean seeded state.

**Next milestone:** M14 — Notifications (in-app + email, wired into triggers from M3/M5/M6/M7/M10).

### 2026-07-15 — M12: Documents

**Done**
- `documents` table: `title`, `category` (string, `DocumentCategory` enum cast — `newsletter`/`annual-report`/`magazine`/`forms`), nullable `description`, `file_path`, nullable `file_size` (bytes), `uploaded_by` nullable + `nullOnDelete`.
- **Deliberate disk decision, distinct from every prior module's attachments**: Notice attachments, Gallery photos, and success-story images all live on the `public` disk (directly linkable). Documents use the `local` disk instead — `storage/app/private`, no public symlink, no direct URL — so every download is forced through `DocumentController::download()` (`Storage::disk('local')->download()`), not a static asset URL. This is the "secure download" the brief asks for.
- **Structure decision, same reasoning as M8/M11**: no draft/pending workflow attached to this module, so it got the Notice Board/Gallery treatment — one controller, `role:super-admin|faculty` route middleware, `DocumentPolicy::manages()` restricting faculty to their own uploads (admin manages all). **No `show` route** — unlike Notice/Gallery, a document has nothing worth a detail page (title/category/description all fit on the index row); the only per-document actions are download/edit/delete, all reachable straight from the index.
- `DocumentService` mirrors `NoticeService`'s shape: direct-property-assignment for `uploaded_by` (no fillable-array FK bug), old file deleted from disk before a replacement is stored on update, file deleted from disk before the row on delete.
- `Document::formattedSize()` — a small model accessor (bytes → B/KB/MB/GB) added because no existing helper did this anywhere in the codebase; kept off the view to avoid duplicating the unit-conversion loop if a second place ever needs it.
- Sidebar: all four roles' "Documents" placeholder (identical markup in all four spots) wired to the real route in one `replace_all` edit.
- `DocumentFactory` + seeder: one document per category (4) from admin, plus one faculty-uploaded "Membership Form" — each seeded with a real minimal PDF written to the private disk (same dummy-PDF bytes already used for Notice attachments in M8), so downloads are genuinely exercisable, not just DB rows.

**Verified — full HTTP click-through against the real LAMPP-backed DB**:
- Admin: index (200), create page (200), edit on any document regardless of uploader (200 — super-admin bypass), download returns `Content-Type: application/pdf` with a correct `Content-Disposition` filename.
- Faculty: create page (200), edit own document (200), edit an admin-uploaded document blocked (403 — "own content only" rule holds, same as Gallery/Notice).
- Alumni: index (200) and download (200 — any authenticated user can read/download), create and edit both blocked (403).
- Unauthenticated request to `/documents` redirected (302), not served.
- Full write path exercised end-to-end as faculty: created a document with a real PDF upload (verified row + file landed on the private disk), updated its title/category (verified DB), deleted it (verified both the row and the on-disk file were gone afterward). One false-start caught during this: an initial upload used a plain-text file renamed to `.pdf`, correctly rejected by the `mimes:pdf,...` rule (422 back to the create form) — confirms the MIME validation is checking real file content, not the extension.
- Reset to `migrate:fresh --seed` afterward; confirmed 5 documents in the clean seeded state.

**Next milestone:** M13 — Feedback (suggestions/complaints/feature requests, admin reply/close, export). *(done — see the M13 entry above.)*

### 2026-07-15 — M11: Gallery

**Done**
- `galleries` (albums: `title`, `category` enum, nullable `description`, `created_by` nullable + `nullOnDelete`) + `gallery_images` (1:many, `cascadeOnDelete` on the parent — same shape as `success_story_images`). Deliberately **no `cover_image_path` column** — the cover is `$gallery->images->first()`, computed dynamically, so there's no separate field that can point at a deleted image or drift out of sync with the actual image set.
- **Structure decision, explicitly matched to M8's reasoning, not M5/M6/M9's**: the brief doesn't attach a specific role or workflow to "Album Management," and unlike Events/Jobs/Success Stories there's no draft/pending state to scope visibility around — so this got the Notice Board treatment: `role:super-admin|faculty` route middleware, `GalleryPolicy::manages()` restricting faculty to their own albums (admin manages all), and **one controller**, not the two-controller split.
- **Route-ordering mistake caught and fixed before it shipped, not after**: first draft split `Route::resource('gallery', ...)` into two separate calls (one for public index/show, one for admin-only create/store/edit/update/destroy under role middleware) — but registering them as two separate resource() calls means the second call's `create` (a literal path) gets registered *after* the first call's `show` (the `{gallery}` wildcard), which would swallow `/gallery/create` as an attempt to look up a Gallery with ID `"create"`. Rewrote using the same explicit-route, correct-ordering pattern already established for Events/Jobs/Success Stories instead of trusting `Route::resource()` split across two middleware groups.
- Lightbox: a single Alpine `x-data="{ lightbox: null }"` on the page, each thumbnail sets `lightbox` to its own full-size URL on click, one shared overlay renders whichever is set — no per-image modal instances needed. Lazy loading is just the native `loading="lazy"` attribute, no JS library.
- `GalleryService::create()`/`update()` use the same direct-property-assignment pattern for `created_by` that's held clean since M8; `attachImages()` uses the relation-based `create()` pattern (safe because `image_path` is genuinely the only fillable field beyond the auto-injected FK), same as Success Stories' image handling.
- **No dashboard changes** — consistent with M9's Success Stories decision, none of the four dashboards' spec'd card lists mention Gallery, so nothing was wired and nothing was invented.
- Sidebar: all four roles' "Gallery" placeholder (identical markup in all four spots) wired to the real route in one `replace_all` edit.
- `GalleryFactory` + seeder: one album per category (5 total), 3–6 generated placeholder photos each via the existing `DummyAvatarGenerator`, created by a mix of admin and faculty.

**Verified — full HTTP click-through against the real LAMPP-backed DB**:
- Student saw all 5 albums (no visibility scoping, as designed) and was blocked (403) from `/gallery/create`.
- Category filter (`?category=reunion`) rendered correctly.
- Album detail page: confirmed `loading="lazy"` present on every image (6 for the reunion album, matching the seeded count exactly).
- Faculty created a new album with a real PNG upload → image correctly attached (`images()->count() === 1`).
- Deleted that image via its own dedicated route → gone.
- Faculty blocked (403) trying to edit an admin-created album — the "own content only" rule holds.
- Faculty deleted their own (now-empty) album → gone.
- Reset to `migrate:fresh --seed` afterward.

**Next milestone:** M12 — Documents (categorized repository: newsletter/annual report/magazine/forms, secure download). *(done — see the M12 entry above.)*

### 2026-07-15 — M10: Donation Management

**Done**
- `donation_campaigns` (`status` enum, excluded from `$fillable`; `created_by` nullable + `nullOnDelete`) + `donations` (`amount`, `payment_method` enum, `transaction_reference`, unique `receipt_number`, `donated_at`; `user_id` nullable + `nullOnDelete` — a financial record should survive account deletion). **Different FK choice than every prior module**: `donations.donation_campaign_id` is `restrictOnDelete()`, not cascade — deleting a campaign must not silently wipe its donation history. `DonationCampaignPolicy::delete()` enforces the same rule at the app layer (only deletable with zero donations), so the constraint is a backstop, not the only guard.
- **Real interpretation call, stated explicitly rather than silently assumed**: nothing in the tech stack is a payment gateway, so this is a donation-*record* system — a donor declares amount + payment method (bKash/Nagad/bank transfer/card/cash) + optional transaction reference, and it's recorded as completed immediately. Mirrors how a lot of university portals actually work (payment happens offline, the portal handles acknowledgment + receipts) rather than building a fake checkout flow nobody asked for.
- `DonationService::donate()` and `createCampaign()` use the direct-property-assignment pattern for FKs — the pattern that's now held clean since M8, no repeat of the earlier bug class. Receipt numbers are generated post-insert (`MBSTU-DON-000123`, zero-padded on the row's own auto-increment ID) since the format depends on knowing the ID first — a genuine two-save sequence, not a workaround.
- **Route parameter bug caught before testing, not during**: `Route::resource('donation-campaigns', ...)` defaults to a snake_case `{donation_campaign}` parameter, but `UpdateDonationCampaignRequest::authorize()` was written looking up `donationCampaign` (camelCase, matching the controller's variable name) — a mismatch that would've made `$this->route(...)` silently return `null` and broken authorization on every campaign edit. Fixed by explicitly setting `->parameters(['donation-campaigns' => 'donationCampaign'])` on the resource route so every reference (controller, manual routes, Form Request) agrees — worth remembering that `Route::resource()`'s default parameter naming doesn't match a manually-typed camelCase model variable, and that mismatch produces no error at boot time, only a silent authorization failure at request time.
- Receipt download is the first real use of `barryvdh/laravel-dompdf`, installed since M0 and untouched until now.
- **Chart.js finally installed** (`npm install chart.js`, `chart.js/auto` convenience import, exposed as `window.Chart`) — deferred since M0 specifically until a real chart was needed, which is now. Added a `@stack('scripts')` slot to `layouts/app.blade.php` (didn't exist before) so page-specific `<script>` blocks can push in cleanly instead of being crammed into the shared layout.
- **Closed two dashboard gaps found while checking placeholders before designing**, not invented as new scope: admin dashboard's "Jobs" stat card was still a placeholder despite M6 shipping (an M6 oversight — only alumni's "Posted Jobs" and student's "Saved Jobs" were wired then, the admin card was missed); and the "Alumni by Department" chart placeholder's stated blocker ("wired once Chart.js is available") is exactly what this milestone fixes, so it got wired alongside Monthly Donations rather than left stale referencing a chart library that now exists.
- Admin dashboard's 8 stat cards reordered to match the brief's exact list (Total Alumni, Verified Alumni, Students, Faculty, Events, Jobs, Donations, Pending Verification) — previously included a "Total Users" card the brief never asked for and was missing "Donations" entirely.
- Alumni dashboard's "Donation History" placeholder wired to a real total + count, linking to their history page.
- Sidebar: admin's Finance section ("Donations" → campaign management, "Reports" → admin reports) wired; added Donations browsing links to alumni/student/faculty (none of them had one before this milestone, even alumni — the brief's "Users can Donate" implies everyone, not just the roles that happened to get a placeholder earlier).

**Verified — full HTTP click-through against the real LAMPP-backed DB**:
- Student saw exactly 2 active campaigns (not the 1 closed one) and was blocked (403) from `/admin/donation-campaigns`.
- Full donate flow via real form POST → persisted with correct amount/user/payment method, receipt number correctly formatted from the row's own ID.
- **Receipt PDF verified with `pdfinfo`/`pdftotext`, not just a 200 status** — `file` reported "0 pages" (a libmagic quirk with dompdf output, not a real defect), so cross-checked with `pdfinfo` (confirmed 1 real page, correct title) and `pdftotext` (confirmed every field — receipt number, amount, donor name, campaign, payment method, transaction reference, date — matches exactly what was submitted).
- A *different* student got 403 trying to download someone else's receipt.
- Admin created/closed/deleted an empty campaign; then confirmed deleting a campaign *with* donations correctly fails (403, `restrictOnDelete` + policy both hold) and the campaign survives.
- Admin report's campaign filter count matched a direct DB query exactly.
- Admin dashboard: confirmed both chart canvases render with 2 `Chart(...)` instantiations, the Jobs card now shows the real DB count (7, matching the fixed gap), and the Donations card shows the correct running total.
- Reset to `migrate:fresh --seed` afterward.

**Next milestone:** M11 — Gallery (albums by category, lazy-loaded image preview).

### 2026-07-15 — M9: Success Stories

**Done**
- `success_stories` (`status` enum, excluded from `$fillable`, `forceFill()`-via-service; `user_id`/`reviewed_by` nullable + `nullOnDelete`) + `success_story_images` (separate 1:many table, `cascadeOnDelete` on the parent — the brief says "Images" plural, so this is a small gallery per story, not a single photo field like most other modules' logos/banners).
- Same authorization shape as Job Portal: create requires `hasRole('alumni') && alumniProfile?->verification_status === Approved`; submitter or super-admin can edit/delete; only super-admin approves/rejects; pending/rejected visible only to the submitter or super-admin (`scopeVisibleTo`, same shape as `Event`/`JobPosting`).
- **Structure decision, explicitly contrasted with M8**: this module *does* get the two-controller split (`SuccessStoryController` for browse/show, `SuccessStoryManagementController` for create/edit/delete/approve/reject/image-removal) because it has the same real visibility-scoping complexity Events/Jobs have (pending/rejected hidden from most viewers) — unlike Notice Board, which correctly used one controller because it had no such scoping. Keeping the two decisions consistent with their actual reasons, not just copying the more recent pattern.
- `SuccessStoryService::create()`/`update()` use the same direct-property-assignment pattern for `user_id` that finally fixed the M3/M5/M7 bug class in M8 — continued here rather than reverting to the array-based `create()` that caused it originally.
- Image management: `attachImages()` uses `$story->images()->create([...])`, which is safe by construction (the `hasMany` relation auto-injects `success_story_id` via `setAttribute()`, bypassing guarding, and `image_path` is the only other field, which *is* fillable) — a second example, after event registrations, of the relationship-based `create()` pattern being safe as long as the non-FK fields are properly fillable.
- Views: index (card grid, first image as thumbnail) and show (full image grid + story text + management actions) follow the Jobs/Events visual pattern; create/edit share `_form.blade.php` with a multi-file `images[]` input and per-image delete buttons for existing images on the edit page.
- **No dashboard changes** — none of the four dashboards' spec'd card lists mention Success Stories, so no placeholder existed to wire and none was invented.
- Sidebar: wired the two existing placeholders (admin's Content section, alumni's "Submit Success Story") to the real route, and — since published stories should be readable by everyone, not just the two roles the brief happened to mention — added a browsing link to student's and faculty's Resources sections too, which didn't have one at all before this milestone.
- `SuccessStoryFactory` (`published()`/`rejected()` states) + seeder: 5 stories from *verified* alumni only (3 published with a generated placeholder image each via the existing `DummyAvatarGenerator`, 1 pending, 1 rejected).

**Verified — full HTTP click-through against the real LAMPP-backed DB**:
- Unverified Demo Alumni got 403 on `/success-stories/create` and saw exactly 3 (published-only) on the index.
- A genuinely verified alumni submitted a story with a real PNG image (not a placeholder file) via multipart upload → persisted as `pending` with the image correctly attached (`images()->count() === 1`).
- Admin's index showed all 6 (5 seeded + 1 new) — approved the new story (→ `published`) and rejected a different pending one (reason persisted).
- A *different* alumni (not the story's author) got 403 trying to edit it — the "own content only" rule holds across alumni accounts, not just against students/faculty.
- Deleted an individual image from the story via its own dedicated route — row actually gone, not just hidden.
- Reset to `migrate:fresh --seed` afterward.

**Next milestone:** M10 — Donation Management (campaigns, donate, history, receipts, admin reports/stats).

### 2026-07-15 — M8: Notice Board

**Done**
- `notices` (`type` enum: notice/circular/scholarship/news/announcement, cast; `attachment_path` nullable on the `public` disk — same precedent as event banners/job logos, nothing here is sensitive like a verification document; `posted_by` FK, nullable + `nullOnDelete`) + `notice_bookmarks` (plain pivot, same shape as `job_bookmarks`).
- **Scope decision**: no draft/publish/archive workflow, unlike Events. The brief's Events section explicitly lists "Create, Edit, Delete, Publish, Archive" as verbs; the Notice Board section just says "Admin and Faculty can publish [types]" with no review/approval language. Modeling a workflow the brief doesn't ask for would be exactly the kind of scope creep the project's own conventions warn against — a notice is live the moment it's created.
- **Structure decision**: a single `NoticeController`, not a public/management pair like Events/Jobs. Those needed the split because of real visibility-scoping complexity (draft events, pending jobs hidden from most viewers); Notice Board has no such scoping — everyone sees everything, only the write actions are role-gated — so forcing the two-controller pattern here would have been structure for its own sake, not because the complexity warranted it.
- `NoticeService::create()`/`update()` use the same direct-property-assignment pattern for `posted_by` that `EventService`/`JobPostingService` already used successfully (`$notice->posted_by = $poster->id`, not passed through the mass-assignment array) — **this is the actual fix for the bug class that hit M3, M5, and M7 three times**, not just a habit to remember. Every prior write-up said "remember to check `$fillable`"; this time the model was structured so there was nothing to remember — a FK set via direct property assignment bypasses guarding entirely regardless of what's in `$fillable`.
- `NoticePolicy`: same `manages()` shape as `EventPolicy`/`JobPostingPolicy` — super-admin can edit/delete any notice, faculty only their own.
- Views: index (search + type filter + bookmarked-only filter, same UI pattern as Jobs), show (content + download + bookmark + management actions), create/edit sharing `_form.blade.php`.
- Dashboards: student gets a real total-notices count, faculty gets a real posted-by-them count.
- Sidebar: all four roles' "Notice Board" placeholder wired to the real route in one `replace_all` edit (identical markup in all four spots, same pattern as M5/M6).
- `NoticeFactory` (type-aware title generation) + seeder: 7 notices across admin and faculty, 2 with a real downloadable PDF attachment (same minimal-valid-PDF-bytes trick used for manual testing in M3, now baked into the seeder itself), 1 pre-bookmarked by the demo student.

**Verified — full HTTP click-through against the real LAMPP-backed DB**:
- Student saw all 7 notices (no visibility scoping, as designed) and was blocked (403) from `/notices/create`.
- Downloaded a real attachment — confirmed via the `file` command ("PDF document, version 1.4"), not just a 200 status.
- Type filter (`?type=scholarship`) count matched a direct DB query exactly.
- Bookmark toggle confirmed via DB before/after; the bookmarked-only filter then showed the correct count (2: the seeded one + the new one).
- Faculty created a notice (persisted correctly), edited it (title/type/content all updated), and deleted it (row actually gone).
- Faculty blocked (403) from editing a notice posted by the admin — the "own content only" policy rule holds.
- Reset to `migrate:fresh --seed` afterward.

**Next milestone:** M9 — Success Stories (verified alumni submit → admin approval → published).

### 2026-07-15 — M7: Mentorship

**Done**
- `mentorship_requests` — `student_id`/`mentor_id` FKs both `cascadeOnDelete` (deliberately different from M5/M6's `nullOnDelete`: an Event or Job is an institutional record that still means something without its creator, but a mentorship request *is* the relationship between two specific accounts — if either disappears, the row no longer represents anything real).
- `App\Enums\MentorshipStatus` (Pending/Accepted/Rejected/Completed), cast, excluded from `$fillable`, transitions only via `MentorshipService` using `forceFill()` — same pattern as every prior workflow module.
- No dedicated "browse mentors" page — the Directory (M4) already does that job. `directory/show.blade.php` grew a "Request Mentorship" button, visible only to students, hidden (replaced with a link to their existing request) if they already have an active one with that mentor.
- `MentorshipRequestPolicy::request(User $user, User $mentor)` — the interesting part is *how* it's invoked: the ability needs a `User` (mentor) as its target, but a `User` model's policy would normally auto-resolve to `UserPolicy`, not `MentorshipRequestPolicy`. Called it as `$this->user()->can('request', [MentorshipRequest::class, $mentor])` — Laravel uses the array's first element (a class string) purely to pick the policy class, then passes the rest of the array as the method's actual arguments. This is the standard pattern for "authorize creating X in the context of Y" and hadn't come up yet in this project.
- One `MentorshipController` (not split like Events/Jobs) — this workflow has no public browsing surface of its own, just accept/reject/schedule/complete/withdraw actions plus an `index` that shows "my requests" scoped to whichever side of the relationship the viewer is on (`forMentor`/`forStudent` scopes).
- Dashboards: alumni get a real pending-mentorship-request count (with a "Needs response" hint when > 0), student get a real total-requests-sent count.
- Sidebar: alumni's "Mentorship Requests" and student's "Find a Mentor" (renamed in effect to "My Mentorship", pointing at the requests list — finding happens via the Directory, same pattern as M6's Jobs sidebar entry) both wired to the real route.
- `MentorshipRequestFactory` (`accepted()`/`rejected()`/`completed()` states) + seeder: one request per status, deliberately including one from the named Demo Student account for easy manual testing.

**Bug caught during verification — and a lesson about a lesson**: `MentorshipRequest::$fillable` was missing `student_id`/`mentor_id`, so `MentorshipService::request()`'s `MentorshipRequest::create([...])` call silently dropped both foreign keys, and the DB rejected the insert outright (`SQLSTATE[HY000]: Field 'student_id' doesn't have a default value` — a hard 500, not a silent data-loss this time, because unlike `EventRegistration`/`AlumniProfile` this table has no nullable/defaulted columns to fall back on). This is the *third* occurrence of this exact bug class (M3's `forceFill` issue, M5's `EventRegistration::user_id`), and this time it slipped through despite explicitly noting in the M5 log that model-vs-service fillable checks should happen *before* running anything. Worth being honest about rather than glossing over: the read-before-run habit didn't actually happen here, and the bug was caught by the test suite (a real 500 response), not by discipline. **Take this as a standing reminder, not just a note**: any time a service calls `Model::create([...])` directly (as opposed to through a relationship like `$parent->children()->create([...])`, which auto-injects its own FK), *every* key in that array — including foreign keys — must be in `$fillable`, and this needs an actual look at the model file, not an assumption, before the first test run.

**Verified — full HTTP click-through against the real LAMPP-backed DB**:
- Directory profile page showed the "Request Mentorship" button for a student viewer.
- New request created via the real form (with message) → persisted correctly (once the bug above was fixed).
- Duplicate request to the same mentor while the first is still pending: blocked at the DB level (count unchanged) with the correct on-page error message.
- Mentor's index correctly scoped to requests directed *at* them (2, not all 4 in the system).
- Full accept → schedule meeting → mark completed chain, each step's data (status, `meeting_scheduled_at`, `meeting_notes`, `completed_at`) verified against the DB directly, not just the HTTP status code.
- Student withdrew their own pending request (row actually deleted) and was correctly blocked (403) from accepting a request where they're not the mentor.
- Reset to `migrate:fresh --seed` afterward.

**Next milestone:** M8 — Notice Board (admin/faculty publish notice/circular/scholarship/news/announcement; search, download attachment, bookmark).

### 2026-07-15 — M6: Job Portal

**Done**
- `job_postings` (deliberately not `jobs` — that name is taken by Laravel's queue table) + `job_bookmarks` (plain pivot, no extra columns, so a `belongsToMany` is enough — unlike `event_registrations` which needed its own model for the `attended` column). Migration filenames generated a full 2 seconds apart on purpose this time, after the M5 timestamp-collision lesson.
- `App\Enums\JobStatus` (Pending/Published/Rejected) and `App\Enums\EmploymentType` (full-time/part-time/contract/internship/remote), both cast. `status` excluded from `$fillable`, transitions only via `JobPostingService::approve()`/`reject()` using `forceFill()` — same pattern as M3/M5.
- `salary`/`experience` deliberately kept as free-text nullable strings, not numeric columns — real job listings say "Negotiable" or "2–4 years" far more often than a clean number.
- **New kind of authorization rule**: `JobPostingPolicy::create()` checks both role *and* profile-verification state — `hasRole('alumni') && alumniProfile?->verification_status === Approved`. Every prior module's "who can do X" was role-only; this is the first one gated by two independent facts about the user. The nullsafe (`alumniProfile?->`) matters here: a brand-new alumni account always has a profile (M3's `ensureProfileExists` guarantees that), but defensive coding for the null case costs nothing and prevents a crash if that invariant is ever violated.
- `JobPosting::scopeVisibleTo()` — same shape as `Event`'s: published visible to all, pending/rejected visible only to the poster or a super-admin.
- Two controllers under one `/jobs/*` route namespace (same shape as Events): `JobPostingController` (index/show/bookmark) + `JobManagementController` (create/store/edit/update/destroy/approve/reject). `/jobs/create` registered before `/jobs/{job}` for the same route-ordering reason as M5.
- Bookmarking: `User::bookmarkedJobs()` / `JobPosting::bookmarkedBy()`, toggle endpoint, `?bookmarked=1` query filter on the same index view rather than a separate "My Bookmarks" page.
- Dashboards: alumni get a real "Posted Jobs" count, student get a real "Saved Jobs" (bookmark) count.
- Sidebar: super-admin's "Jobs" (Content section), alumni's "Post a Job" (renamed in effect to a general "Jobs" browse+create entry, matching the Events sidebar pattern where the create action lives as a button on the index page, not a separate nav item), and student's "Job Board" all wired to the real route.
- `JobPostingFactory` (`published()`/`rejected()` states) + seeder: job postings are seeded as coming *only* from the already-`approved()` alumni profiles, not just any alumni — so the seeded data itself respects the same rule the Policy enforces, rather than accidentally modeling an impossible state.

**Verified — full HTTP click-through against the real LAMPP-backed DB**:
- Demo Alumni (whose seeded profile is still `pending`, i.e. *not* verified) got **403** on `/jobs/create` — the two-condition Policy check actually holds, not just the role check.
- That same unverified alumni's `/jobs` index showed exactly 4 (the published-only count) — no pending/rejected jobs from other posters leaked through.
- A genuinely verified alumni got 200 on `/jobs/create`, and their index count was 4 + exactly their own non-published job count (6 total) — confirmed by cross-checking the DB directly, not just eyeballing the number.
- Created a new job via real form POST → persisted as `pending`.
- Admin's index showed all 8 (unrestricted visibility) — approved the new job (→ `published`) and rejected a different pending one (reason persisted correctly).
- Student bookmarked a job (toggle confirmed via DB before/after), the `?bookmarked=1` filter showed the right count.
- Student blocked from editing another user's job (403) and from approving a job — the *second* approve attempt used a valid CSRF token specifically to distinguish a real 403 from an incidental 419 (the first attempt, using a raw `curl -X POST` with no token, got 419 and would have been a false-positive "authorization works" if taken at face value).
- Reset to `migrate:fresh --seed` afterward.

**Next milestone:** M7 — Mentorship (student requests a mentor → alumni accepts/rejects → scheduled → completed).

### 2026-07-15 — M5: Events

**Done**
- `events` + `event_registrations` migrations. **Note the filenames**: both were auto-generated with the identical timestamp `2026_07_14_181641`, which would have made `event_registrations` (alphabetically first) run *before* `events` and break its FK — renamed to `..._181642_...` to force correct order. Worth remembering if two migrations are ever generated in the same `php artisan make:migration` batch/minute again.
- `App\Enums\EventStatus` (Draft/Published/Archived), cast on `Event`, excluded from `$fillable` (status transitions only happen through `EventService::publish()`/`archive()` via `forceFill()` — same pattern as `VerificationStatus` in M3).
- `Event::scopeVisibleTo(User $user)` — published + archived visible to everyone; draft visible only to its creating faculty member or any super-admin. `Event::scopePublished()` for the simpler public-facing case.
- `on-delete` decision for `events.created_by`: `nullable()->nullOnDelete()`, not cascade — an event (and its registrations, which people may be counting on) is an institutional record that should survive its creator's account being deleted, unlike e.g. a personal profile.
- `EventPolicy`: `manages()` private helper (super-admin always; faculty only for events they created) backs `update`/`delete`/`publish`/`archive`/`manageParticipants`. This is the first module where two roles (`super-admin`, `faculty`) share management rights — route middleware is `role:super-admin|faculty` (Spatie's pipe syntax for "any of these"), with the Policy adding the "own event only" restriction faculty needs that middleware alone can't express.
- `EventService` — create/update/publish/archive/delete/register/cancelRegistration/markAttendance. `register()` throws `ValidationException` (closed/full/duplicate), caught in the controller and turned into a flashed form error — not a raw 500 or silent no-op.
- Two controllers, one route namespace: `EventController` (index/show/register/cancelRegistration — anyone) and `EventManagementController` (create/store/edit/update/destroy/publish/archive/participants/exportParticipants/markAttendance — admin+faculty). Both live under `/events/*`, not split into `/events` vs `/admin/events`, so browsing and managing feel like one coherent area rather than two apps glued together. Route order matters here: `/events/create` is registered before `/events/{event}` specifically to avoid Laravel trying to route-model-bind an Event with ID `"create"`.
- `EventParticipantsExport` (`FromCollection`/`WithHeadings`) — first real use of `maatwebsite/excel`, installed since M0 but untouched until now.
- Views: `events/{index,show,create,edit,_form,participants}.blade.php`. Sidebar's four "Events" placeholders (one per role) wired to the real route.
- Dashboards: admin gets a real Events count; alumni/student get a real upcoming-published-events count; faculty gets their own published-events count. Also fixed a leftover from M4 — faculty dashboard's "Alumni Statistics" card was still a placeholder even though the Directory (M4) already made that data available; wired it to the real verified-alumni count while touching this controller anyway.
- `EventFactory` (`published()`/`archived()` states) + seeder: 8 events (4 published, 2 draft, 2 archived) across faculty and admin creators, with registrations + some attendance marks on the first published event.

**Bugs caught during verification (both fixed before they'd have surfaced for a real user)**
1. `EventRegistration::$fillable` was missing `user_id`. `EventService::register()` calls `$event->registrations()->create(['user_id' => $user->id])` — the relation's own FK (`event_id`) gets set automatically via `setAttribute()` regardless of guarding, but `user_id` is just a normal attribute in that call and needs to be fillable, same class of bug as the M3 `forceFill` issue. Caught by re-reading the model against the service *before* running it, not by a failed test — worth doing that read deliberately on every new pivot-style model going forward.
2. Migration timestamp collision (see above) — caught by `ls`-checking the generated filenames before running `migrate`, not by a failed migration.

**Verified — full HTTP click-through against the real LAMPP-backed DB**:
- Student sees 6 events (4 published + 2 archived, correctly excluding the 2 drafts); faculty sees all 8 (their own drafts included) — confirms `scopeVisibleTo` works both directions.
- Student blocked (403) from `/events/create`.
- Faculty created a new event (defaults to `draft`, correct `created_by`), published it, viewed participants (correct count), **downloaded a real `.xlsx` file** (confirmed via the `file` command: "Microsoft Excel 2007+", not just a 200 status), and marked a registrant's attendance — persisted correctly.
- Faculty blocked (403) from editing an event created by the admin — the "own event only" policy rule actually holds, not just the weaker "any faculty" role-middleware check.
- Student registration: fresh registration succeeded, immediate duplicate attempt correctly rejected **at the database level** (row count unchanged) — first checked this the wrong way (`curl -d` without `-L` doesn't follow the redirect, so the flashed error never appeared in that response body, which looked like the error wasn't rendering when it actually just hadn't been fetched yet); redid it with `-L` and confirmed the actual on-page message ("You are already registered for this event.") renders correctly. Cancellation also confirmed.
- Reset to `migrate:fresh --seed` afterward.

**Next milestone:** M6 — Job Portal (verified alumni post jobs → pending approval → admin approves → published; students browse/search/bookmark).

### 2026-07-15 — Directory polish: dummy phone/photo + contact info (user-requested, between M4 and M5)

**Done**
- `resources/fonts/DejaVuSans-Bold.ttf` (+ `DejaVuSans-LICENSE.txt`) bundled into the repo — permissively licensed, redistributable. Bundled rather than referencing a system font path, since the seeder needs to run on the user's real LAMPP machine, not just this sandbox, and system font availability can't be assumed.
- `Database\Support\DummyAvatarGenerator` (new `database/support/` PSR-4 root, registered in `composer.json`) — generates a 256×256 PNG, initials over a deterministic color (picked by `crc32($name)` so the same name always gets the same color), using GD + the bundled TTF font. Seed-only utility, never touched at runtime.
- `UserFactory` now generates a plausible Bangladeshi-format phone (`+880` + `numerify('1#########')`) for every factory-created user — applies automatically to all seeded accounts, named and bulk alike.
- `DatabaseSeeder` generates and attaches a dummy avatar (via `Storage::disk('public')`) for every seeded alumni user (17 total).
- Contact info surfaced: directory index cards show email (+ phone if set) as plain text; directory show page gets a dedicated Contact section with working `mailto:`/`tel:` links; admin's alumni-verification show page now also displays phone next to email.
- Closed a real gap from M0: `phone` has existed on `users` since the very first migration but was never actually editable anywhere. Added it to `ProfileUpdateRequest` (nullable) and the account-settings form — `ProfileController@update` needed no change since it already mass-fills whatever the Form Request validates.

**Verified against the real LAMPP DB**: photo files are genuine 256×256 PNGs (`file` command), served correctly over HTTP through the `public` storage symlink; all 33 seeded users have a phone, all 17 alumni have a photo; directory index/show render both correctly with working `mailto:`/`tel:` links; account-settings phone update persists correctly. One test-only false alarm — an initial curl test showed the `+` stripped from a submitted phone number, which looked like a bug but was actually `curl -d` encoding a literal `+` as a space (that's how `application/x-www-form-urlencoded` works); confirmed the real app is correct by resending with `--data-urlencode`.

### 2026-07-15 — M4: Alumni Directory

**Done**
- `AlumniProfile::scopeApproved()` — reusable query scope, also used to clean up the two raw `where('verification_status', ...)` calls in `DashboardController` from M3.
- `DirectoryController@index` — search (name via `users.name`, student ID, department, batch, session, graduation year, company, country, district, skills — all `LIKE`/exact match on `alumni_profiles` columns) + sort (latest joined / name / graduation year), 12/page pagination. Name-sort uses an `orderBy(subquery)` against `users.name` rather than a join, specifically to avoid pagination's internal `COUNT()` query interacting oddly with a joined `select()` — subquery sort is the safer pattern here.
- `DirectoryController@show` — read-only detail page, scoped to `approved()` so a guessed ID for a pending/rejected profile 404s instead of leaking it.
- **Access decision** (not fully specified in the brief): "public" directory means visible to any authenticated role, not unauthenticated — there's no public marketing site in this app's scope, everything else requires login. Route middleware is just `['auth', 'verified']`, no `role:` restriction.
- No new Policy — this is query-scoping ("only ever return approved rows"), not a per-resource authorization decision, so a Policy would be the wrong tool here.
- `directory/index.blade.php` (filter bar + card grid + empty state) and `directory/show.blade.php` (full profile detail: bio, skills, social links, academic, professional).
- Sidebar's "Alumni Directory" link (previously a `soon` placeholder in all four roles' sections) wired to `directory.index` everywhere — one `replace_all` edit since the placeholder markup was byte-identical in all 4 spots.

**Verified — full HTTP click-through against the real LAMPP-backed DB, logged in as a student (deliberately not the admin, to prove the "any role" access decision actually works)**:
- `/directory` unfiltered count (4) matched `AlumniProfile::approved()->count()` exactly.
- Department filter count matched a direct DB query for the same filter exactly.
- Sort by name returned alphabetically ordered results.
- Name search found the expected profile.
- A filter with no matches rendered the empty state, not an error.
- Viewing an `approved` profile's detail page: 200. Viewing a `pending` profile's detail page by ID: **404**, confirming unverified alumni aren't discoverable by guessing IDs.
- Re-checked the admin dashboard's "Verified Alumni" stat card after the `scopeApproved()` refactor — still correct (4).
- This milestone was read-only (no mutations), so no DB reset was needed afterward — confirmed counts unchanged (33 users, 17 profiles) before moving on.

**Next milestone:** M5 — Events (CRUD + publish/archive by admin/faculty, registration/capacity/deadline, participant export, attendance marking).

### 2026-07-14 — M3: Alumni Profile + Verification workflow

**Done**
- `alumni_profiles` migration: 1:1 with `users` (FK unique, cascade delete), personal/academic/professional/social/additional fields all nullable (row exists from day one, filled progressively), plus `verification_status`/`verification_document_path`/`rejection_reason`/`reviewed_by`/`reviewed_at`. `App\Enums\VerificationStatus` (Pending/Approved/Rejected) cast on the model, matching the project's state-modeling convention.
- `App\Models\AlumniProfile` — `skillList()` (comma-separated → array, no dedicated tags table yet, per the tech-decision note in `DESIGN.md`) and `completionPercentage()` (drives the alumni dashboard stat card).
- `App\Policies\AlumniProfilePolicy` — owner or super-admin can view; only owner can update; only super-admin can `review` (approve/reject).
- `App\Services\AlumniProfileService` — `ensureProfileExists`, `updateProfile`, `uploadProfilePhoto` (public disk), `uploadVerificationDocument` (private `local` disk, resets status to pending + clears rejection_reason on resubmit), `approve`, `reject`.
- **Registration flow changed**: `auth/register.blade.php` now asks "I am a..." (Alumni/Student — faculty/admin stay staff-created via M2). `RegisteredUserController` assigns the chosen role and, for alumni, calls `ensureProfileExists()` immediately. This closes a real gap from M1: public registration previously assigned **no role at all**, which would have 403'd on `/dashboard`. `UserManagementService::create/update` also call `ensureProfileExists()` when an admin sets role=alumni.
- Self-service: `Alumni\AlumniProfileController` under `/alumni/profile` (`role:alumni` middleware) — edit/update (big sectioned form: personal/academic/professional/social/additional), separate photo upload and document upload endpoints (separate `multipart` forms, separate Form Requests: `UploadProfilePhotoRequest` image-only 2MB, `UploadVerificationDocumentRequest` pdf/jpg/png 5MB).
- Admin: `Admin\AlumniVerificationController` under `/admin/alumni-verifications` (`role:super-admin`) — status-filtered index (defaults to `pending`), a show page with full profile detail + document download link + inline approve/reject (reject requires a reason, `RejectAlumniProfileRequest`). Document download streams through `Storage::disk('local')->download()` behind the `review` policy check — never a public URL.
- Dashboards updated with real data: admin's Verified Alumni / Pending Verification cards now query `AlumniProfile` counts instead of placeholders; alumni dashboard shows real `completionPercentage()` and a live verification-status badge linking to the profile page.
- Sidebar: super-admin's "Alumni Verification" and alumni's new "My Profile" links wired to the real routes.
- `AlumniProfileFactory` (`approved()`/`rejected()` states) + `DatabaseSeeder` updated: the demo alumni account and a spread of bulk-seeded alumni now get profiles in a realistic mix of pending/approved/rejected, so the verification queue has real data to review.

**Bugs caught during verification (both fixed, not just noted)**
1. **Document upload silently didn't persist.** `AlumniProfileService::uploadVerificationDocument/approve/reject` called `$profile->update([...])` with columns (`verification_status`, `verification_document_path`, `reviewed_by`, ...) that are deliberately absent from `AlumniProfile::$fillable` — correct for the *self-service* update path (a user must never mass-assign their own verification status), but that guard also silently dropped the fields when the *service* tried to write them, even though the file itself was actually saved to disk. The controller happily redirected with a success toast while the DB row was untouched — caught only because I checked the DB directly after the HTTP call, not just the HTTP status code. Fixed by switching those three service methods to `forceFill()->save()`, since they're trusted system-controlled writes, not user input flowing through mass assignment. `$fillable` stays scoped to exactly what the profile-edit Form Request validates.
2. Initial document-upload test used a plain text file renamed `.pdf`, which correctly failed Laravel's real (magic-byte, not extension) MIME validation — not a bug, but worth remembering when hand-testing file uploads with curl: use a real minimal PDF (`%PDF-1.4...`), not a renamed `.txt`.

**Verified — full HTTP click-through against the real LAMPP-backed `mbstu_alumni` DB** (migrate:fresh --seed, `php artisan serve`, cookie-jar curl, real files uploaded via multipart):
- Registered a brand-new Alumni account → role assigned, profile auto-created with `status=pending`, confirmed via `tinker`.
- Registered a brand-new Student account → role assigned, no profile created, dashboard reachable (no verification gate for students, per the brief).
- Confirmed Breeze's `verified` middleware correctly gates `/dashboard` for both new registrations until email-verified (expected behavior, not a bug — had to mark them verified via `tinker` since this sandbox has no real mail transport).
- Alumni profile update: posted real field values (student_id, department, skills, etc.) → persisted correctly, `completionPercentage()` computed 85% for a mostly-filled profile.
- Alumni photo upload and document upload: both stored on the correct disk (`public` vs private `local`) and the file actually exists on disk (confirmed via `find`), not just a DB-path claim.
- Admin verification index: pending-filter count matched exactly (12 = 11 seeded pending + 1 fresh registration).
- Admin show page → document download returns the actual PDF bytes (`file` command confirms) when requested by the super-admin.
- **Authorization boundary**: a `student`-role user got 403 on both `/alumni/profile` and the admin document-download URL. A `super-admin` downloading the same document got 200.
- Approve → status flips to `approved`, `reviewed_by`/`reviewed_at` set correctly.
- Reject without a reason → correctly rejected by Form Request validation (no state change). Reject with a reason → status flips to `rejected`, reason persisted.
- Cleaned up all test accounts' uploaded files and reset to `migrate:fresh --seed` afterward.

**Next milestone:** M4 — Alumni Directory (public search/sort over verified alumni only — reads `alumni_profiles` where `verification_status = approved`, no new table).

### 2026-07-14 — M2: User Management

**Done**
- `app/Http/Controllers/Controller.php`: added the `AuthorizesRequests` trait back (Laravel 12's default base `Controller` ships empty) so `$this->authorize()` works.
- `App\Policies\UserPolicy`: `viewAny`/`view`/`create`/`update` require `super-admin`; `delete`/`toggleStatus` additionally block deleting/deactivating yourself or the last remaining `super-admin`.
- `App\Http\Requests\Admin\{Store,Update}UserRequest`: own `authorize()` (`$this->user()->can(...)`) so create/update are policy-gated without needing `authorizeResource()`.
- `App\Services\UserManagementService`: create/update/toggleStatus/delete — password hashing and role sync live here, not in the controller.
- `App\Http\Controllers\Admin\UserController` (resource, `except('show')` — no standalone detail page, edit covers it) under `routes/admin.php`, `prefix('admin')->middleware(['auth','verified','role:super-admin'])`.
- `admin/users/{index,create,edit,_form}.blade.php` — searchable/filterable/paginated table, activate/deactivate, delete via Breeze's existing `x-modal` confirm pattern (per-row named modal).
- New reusable components: `x-toast` (session-flash success/error, mounted once in `layouts/app.blade.php`), `x-empty-state`.
- Sidebar's "Users" link now points at `admin.users.index` for `super-admin` instead of the `#`/Soon placeholder.
- `DatabaseSeeder`: added 12 alumni + 8 students + 5 faculty + 4 inactive alumni via factory, so the list/search/filter/pagination UI has real data to exercise (33 users total after seeding).

**Bugs caught during verification (both fixed, not just noted)**
1. `authorizeResource()` in the controller constructor threw `Call to undefined method UserController::middleware()` — Laravel 12's slimmed base `Controller` doesn't have the old instance-method `middleware()` that trait relies on internally. Fixed by dropping `authorizeResource()` and calling `$this->authorize()` explicitly per action instead (more idiomatic for 12 anyway).
2. `admin/users/_form.blade.php` threw `Undefined variable $user` on the **create** page (no `$user` passed): `old('role', $user->roles->first()?->name ?? '')` chains a method call (`->first()`) after the possibly-undefined `$user`, and PHP's `??`/nullsafe notice-suppression only covers *simple* variable/property access, not once a method call is chained in. Fixed by normalizing at the top of the partial: `$user = $user ?? null;` (safe — bare variable access) then using `$user?->` throughout and a precomputed `$currentRole` local.

**Verified — full HTTP click-through against the real LAMPP-backed `mbstu_alumni` DB** (migrate:fresh --seed, `php artisan serve`, cookie-jar curl through actual middleware, not shortcuts):
- Index: 33 total, 15/page pagination correct (16 `<tr>` = 1 header + 15 rows).
- Search (`?search=Demo`) returned exactly the 3 "Demo *" seeded users.
- Filter (`?role=alumni&status=inactive`) returned exactly the 4 seeded inactive alumni.
- Create → new user persisted with the correct role.
- Edit/update → name, role changed and persisted correctly.
- Toggle-status → flipped active→inactive correctly.
- Delete → row actually removed from the DB.
- A `student`-role user hit `/admin/users` and got **403** (route-level `role:super-admin` middleware).
- The `super-admin` tried to delete **themselves** and got **403** (policy guard) — account still exists afterward, confirmed via `tinker`.
- Reset to a clean `migrate:fresh --seed` afterward so the DB isn't left in the test-mutated state.

**Next milestone:** M3 — Alumni Profile + Verification workflow (`alumni_profiles` table, personal/academic/professional/social/skills/bio fields, document upload, pending→approved/rejected admin review).

### 2026-07-14 — M1: Role-based dashboard shell

**Done**
- `bootstrap/app.php`: registered Spatie's `role`/`permission`/`role_or_permission` middleware aliases (Laravel 11+ style, no `Kernel.php`).
- `resources/js/app.js`: added `Alpine.store('sidebar')` (open/toggle) alongside the existing `darkMode` store.
- `resources/views/layouts/app.blade.php` (the `<x-app-layout>` shell used by every authenticated page) rebuilt as sticky sidebar + topbar + optional `$breadcrumbs`/`$header` slots — ported directly from `static_prototype_folder/pages/dashboard/admin.html`, same Tailwind tokens and Alpine stores.
- New components: `x-sidebar-nav` (role-aware via `@role()`), `x-sidebar-link`, `x-breadcrumbs`, `x-stat-card`, `x-stat-card-placeholder`.
- Retired `layouts/navigation.blade.php`, `components/nav-link.blade.php`, `components/responsive-nav-link.blade.php` — dead code now that the sidebar replaces Breeze's top nav; confirmed no other view referenced them before deleting.
- `App\Http\Controllers\DashboardController@index` resolves the user's role (`App\Enums\RoleName`) and renders `dashboard.{admin,alumni,student,faculty}`; unrecognized/no role throws a 403 rather than silently guessing. Admin dashboard queries real counts (`User::role(...)->count()`) since `users`+Spatie tables already exist; every other card is an honest `x-stat-card-placeholder` naming the milestone that will back it (no fabricated numbers).
- `routes/web.php`: `/dashboard` now points at `DashboardController@index` instead of a closure returning a single shared view.
- `resources/views/profile/edit.blade.php` updated to the new layout slots (breadcrumbs + header), dropped its now-redundant `max-w-7xl` wrapper since `<main>` in the new layout already applies page padding.

**Verified**
- `npm run build` clean.
- `php artisan view:cache` compiled every Blade template (including all new components/views) with no syntax errors, then cleared again for local dev.
- `php -l` clean on all new/changed PHP files.
- `php artisan route:list` confirms `/dashboard` → `DashboardController@index`.
- **Full logged-in click-through, done the same day once the user pointed this sandbox at their LAMPP MySQL** (`mbstu_alumni` DB, port 3306 reachable — this sandbox has no Docker, so LAMPP's MySQL is the real backing DB here, not just for the user's separate machine): `php artisan migrate --force` (4 migrations, clean) → `php artisan db:seed --force` → started `php artisan serve`, logged in as all 4 seeded users via real HTTP requests (cookie-jar curl, following the actual `auth`+`verified` middleware, not a tinker shortcut), fetched `/dashboard` for each. All 4 returned 200 with zero server-error strings in the HTML. Confirmed: sidebar section headers differ correctly per role (super-admin gets Management/Content/Finance, alumni gets Alumni/Community, student gets Student/Resources, faculty gets Faculty/Resources — all get Overview+Account); admin dashboard's real stat cards show the correct seeded counts (1 alumni, 1 student, 1 faculty, 4 total users); breadcrumb renders "Admin / Dashboard"; built CSS/JS assets return 200. Not yet checked in an actual browser: dark-mode toggle click behavior and mobile sidebar open/close animation (HTML/JS is present and correct per the earlier Blade-compile check, but nobody has clicked it) — low risk, worth a 30-second look next time this is opened in a browser.

**Next milestone:** M2 — User Management (admin CRUD, role assignment, activate/deactivate, search/filter/paginate).

### 2026-07-14 — M0: Project scaffolding

**Done**
- Laravel 12 (PHP 8.5.4) installed at repo root, git initialized.
- Breeze `blade` stack installed with `--dark` (Blade + Tailwind v3 + Alpine.js).
- Installed: `spatie/laravel-permission`, `barryvdh/laravel-dompdf`, `maatwebsite/excel` (+ configs published: `config/permission.php`, `config/dompdf.php`, `config/excel.php`).
- `users` table (pre-migration, edited in place — safe since it hadn't run yet) extended with `phone`, `profile_photo_path`, `status` enum (`active`/`inactive`).
- `App\Models\User` implements `MustVerifyEmail`, uses `HasRoles`.
- `App\Enums\RoleName` (backed enum: `super-admin`, `alumni`, `student`, `faculty`) + `RoleSeeder` + `DatabaseSeeder` seeds one demo user per role (`admin@mbstu-alumni.test` / `alumni@...` / `student@...` / `faculty@...`, password `password` — **local/dev only, do not ship this seeder as-is to production**).
- Dark/light mode: `tailwind.config.js` set to `darkMode: 'class'` + `primary` color alias (indigo); `resources/js/app.js` registers `Alpine.store('darkMode')`; no-FOUC inline script added to `layouts/app.blade.php` and `layouts/guest.blade.php`; `x-dark-mode-toggle` component added and wired into `layouts/navigation.blade.php` (desktop + mobile).
- `static_prototype_folder/` created with a working gallery (`index.html`), a login mockup, and an admin-dashboard mockup (sticky sidebar, topbar, breadcrumbs, stat cards, chart placeholders, data table) — Tailwind CDN + Alpine CDN, zero build step, dark mode wired the same way as the real app.
- `.claude/CLAUDE.md`, `.claude/DESIGN.md`, `.claude/PLAN.md` written.
- `npm run build` verified clean. `php artisan about` verified the app boots and registers Spatie Permission.

**Not done / explicitly deferred**
- No migrations have actually been run — this sandbox has no MySQL server and no usable docker socket (permission denied, no passwordless sudo). Run `php artisan migrate --seed` on the real dev machine before doing anything else.
- No sidebar/topbar dashboard layout in the real Blade app yet — Breeze's default simple top nav is still what's live; the sticky-sidebar shell is M1.
- Chart.js not added to `package.json` yet (intentionally — see `DESIGN.md` → Architecture decisions → Frontend).
- No module (Alumni Profile, Events, Jobs, ...) has been built. `static_prototype_folder/` only has auth + admin-dashboard mockups so far.

**Environment quirks worth remembering**
- PHP 8.5 in this sandbox has no native `ext-mbstring` or `ext-curl`; mbstring only works because `phpoffice/phpspreadsheet` pulls in `symfony/polyfill-mbstring` transitively. See `CLAUDE.md` → Environment quirks.
- `composer require` needed `--ignore-platform-req=ext-mbstring` (always) and, for `maatwebsite/excel` specifically, also `--ignore-platform-req=php` (phpspreadsheet's published constraint caps at `<8.5.0`).

**Next milestone:** M1 — role-based dashboard shell (sticky sidebar, topbar, breadcrumbs, per-role route/middleware groups, empty dashboards). Prototype it in `static_prototype_folder/pages/dashboard/` first, then port to Blade.
