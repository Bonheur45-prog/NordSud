# Press Pages (listing + detail) — changes

Drop these files into your project at the matching paths. Two files are
**edits to existing files**, not new ones — check the diff before
overwriting:
- `app/Filament/Resources/PressPosts/PressPostResource.php`
- `app/Http/Middleware/SetLocale.php`
- `routes/web.php`
- `resources/css/app.css`

Everything else is new.

## What this adds

**Schema**: `press_authors`, `press_tags`, `press_post_press_tag` pivot,
`press_comments`, plus `author_id` / `is_featured` on `press_posts`.

**Admin (`/admin`)**:
- **Press Authors** and **Press Tags** — new resources under the sidebar.
- **Press Posts** — the editor now has an Author select, multi-select
  Tags, a "Popular Posts" featured toggle, and the body field is a rich
  editor (use the quote-block button for pull-quotes). A **Comments**
  tab lets you moderate — approve, edit, backdate, or delete comments.

**Public site**:
- `/{locale}/press` — hero, search (`?q=`), tag filter (`?tag=slug`),
  card grid with date badges, sidebar (search / popular posts / popular
  tags).
- `/{locale}/press/{slug}` — cover image, author card, tags, share
  links (Facebook/WhatsApp/X/LinkedIn), previous/next navigation,
  comments, and a **working** comment form: submissions are held
  unapproved until you approve them in `/admin` → Press Posts → open a
  post → Comments tab. A honeypot field blocks basic bots.

**Fix included**: `SetLocale` middleware now also sets Carbon's locale
per-request. Without this, `translatedFormat()` (used for article dates)
would silently render in English even on `/fr/...` pages — a latent bug
in the original stub views, not something this change introduced.

## After copying the files in

```bash
npm run build
php artisan migrate --force
php artisan db:seed --class=PressContentSeeder --force
```

The seeder creates 2 authors, 8 tags, and 4 real NSI Group press posts
(partnership renewal, EMV compliance, a security-audit checklist, and a
fraud-prevention piece) with realistic comment data. It's idempotent —
safe to re-run without duplicating anything already in the database.

Image fields (author avatars, post cover images) are left blank — upload
those via `/admin` as with everything else on this project.

## Running the tests

```bash
php artisan test --filter=PressPageBuildoutTest
php artisan test --filter=PressFlowTest
```

21 tests total, all passing against a clean migration: search, tag
filtering, the popular-posts sidebar, author/tag/comment display,
prev/next navigation, the full comment moderation flow (submit → hidden
→ approved → visible), the honeypot, validation, and the new admin
resources. (There is one pre-existing, unrelated failure in
`HomePageContentTest` — a `press_spotlight` key mismatch on the Home
page seeder — not touched or introduced by this change.)
