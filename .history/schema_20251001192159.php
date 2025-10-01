<?php
// Use this file to create separate migration files. Each class below should be saved
// in its own migration file, following Laravel timestamp naming convention.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| 1) users
|--------------------------------------------------------------------------
*/
class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('uid', 64)->primary(); // keep firebase uid or custom
            $table->string('email', 254)->unique()->nullable();
            $table->string('display_name', 200)->nullable();
            $table->text('password_hash')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('role_default', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('metadata')->nullable();
            $table->bigInteger('created_at')->nullable();
            $table->bigInteger('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}

/*
|--------------------------------------------------------------------------
| 2) roles
|--------------------------------------------------------------------------
*/
class CreateRolesTable extends Migration
{
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->string('role_id', 64)->primary();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
            $table->json('permissions')->nullable();
            $table->bigInteger('created_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
}

/*
|--------------------------------------------------------------------------
| 3) user_roles
|--------------------------------------------------------------------------
*/
class CreateUserRolesTable extends Migration
{
    public function up()
    {
        Schema::create('user_roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uid', 64);
            $table->string('role_id', 64);
            $table->bigInteger('assigned_at')->nullable();

            $table->foreign('uid')->references('uid')->on('users')->onDelete('cascade');
            $table->foreign('role_id')->references('role_id')->on('roles')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_roles');
    }
}

/*
|--------------------------------------------------------------------------
| 4) sessions (simple session/token store)
|--------------------------------------------------------------------------
*/
class CreateSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('session_id', 128)->primary();
            $table->string('uid', 64)->nullable();
            $table->string('ip_addr', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->bigInteger('created_at')->nullable();
            $table->bigInteger('expires_at')->nullable();
            $table->json('meta')->nullable();

            $table->index('uid');
            $table->foreign('uid')->references('uid')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sessions');
    }
}

/*
|--------------------------------------------------------------------------
| 5) santri (students)
|--------------------------------------------------------------------------
*/
class CreateSantriTable extends Migration
{
    public function up()
    {
        Schema::create('santri', function (Blueprint $table) {
            $table->string('santri_id', 64)->primary();
            $table->string('name', 200);
            $table->string('nis', 50)->nullable();
            $table->string('room', 50)->nullable();      // kamar
            $table->string('maskan', 50)->nullable();
            $table->string('phone', 50)->nullable();
            $table->json('parent_info')->nullable();
            $table->json('extra')->nullable();
            $table->bigInteger('created_at')->nullable();
            $table->bigInteger('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('santri');
    }
}

/*
|--------------------------------------------------------------------------
| 6) classes
|--------------------------------------------------------------------------
*/
class CreateClassesTable extends Migration
{
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->string('class_id', 64)->primary();
            $table->string('title', 200)->nullable();
            $table->string('level', 50)->nullable();
            $table->string('code', 50)->nullable();
            $table->string('color', 20)->nullable();
            $table->json('meta')->nullable();
            $table->bigInteger('created_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('classes');
    }
}

/*
|--------------------------------------------------------------------------
| 7) class_members
|--------------------------------------------------------------------------
*/
class CreateClassMembersTable extends Migration
{
    public function up()
    {
        Schema::create('class_members', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('class_id', 64);
            $table->string('santri_id', 64);
            $table->bigInteger('joined_at')->nullable();
            $table->bigInteger('left_at')->nullable();

            $table->index('class_id');
            $table->index('santri_id');
            $table->foreign('class_id')->references('class_id')->on('classes')->onDelete('cascade');
            $table->foreign('santri_id')->references('santri_id')->on('santri')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('class_members');
    }
}

/*
|--------------------------------------------------------------------------
| 8) devices
|--------------------------------------------------------------------------
*/
class CreateDevicesTable extends Migration
{
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->string('device_id', 128)->primary();
            $table->string('name', 200)->nullable();
            $table->string('location', 200)->nullable();
            $table->json('meta')->nullable();
            $table->bigInteger('last_seen')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('devices');
    }
}

/*
|--------------------------------------------------------------------------
| 9) rfid_tags
|--------------------------------------------------------------------------
*/
class CreateRfidTagsTable extends Migration
{
    public function up()
    {
        Schema::create('rfid_tags', function (Blueprint $table) {
            $table->string('uid', 128)->primary();
            $table->string('santri_id', 64)->nullable();
            $table->bigInteger('issued_at')->nullable();
            $table->bigInteger('revoked_at')->nullable();
            $table->json('meta')->nullable();

            $table->foreign('santri_id')->references('santri_id')->on('santri')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('rfid_tags');
    }
}

/*
|--------------------------------------------------------------------------
| 10) attendance_sessions
|--------------------------------------------------------------------------
*/
class CreateAttendanceSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->string('session_key', 128)->primary();
            $table->string('title', 200)->nullable();
            $table->bigInteger('started_at')->nullable();
            $table->bigInteger('ended_at')->nullable();
            $table->string('created_by', 64)->nullable();
            $table->json('meta')->nullable();

            $table->foreign('created_by')->references('uid')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_sessions');
    }
}

/*
|--------------------------------------------------------------------------
| 11) attendance_records
|--------------------------------------------------------------------------
*/
class CreateAttendanceRecordsTable extends Migration
{
    public function up()
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->string('id', 128)->primary(); // preserve firebase key if any
            $table->string('session_key', 128)->nullable();
            $table->string('santri_id', 64)->nullable();
            $table->string('name', 200)->nullable();
            $table->string('kamar', 50)->nullable();
            $table->string('maskan', 50)->nullable();
            $table->string('device_id', 128)->nullable();
            $table->string('rfid_uid', 128)->nullable();
            $table->string('recorded_by', 64)->nullable();
            $table->bigInteger('ts')->nullable(); // epoch ms
            $table->string('status', 50)->nullable();
            $table->json('meta')->nullable();

            $table->index(['santri_id', 'ts']);
            $table->index('session_key');

            $table->foreign('session_key')->references('session_key')->on('attendance_sessions')->onDelete('set null');
            $table->foreign('santri_id')->references('santri_id')->on('santri')->onDelete('set null');
            $table->foreign('device_id')->references('device_id')->on('devices')->onDelete('set null');
            $table->foreign('rfid_uid')->references('uid')->on('rfid_tags')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_records');
    }
}

/*
|--------------------------------------------------------------------------
| 12) agendas
|--------------------------------------------------------------------------
*/
class CreateAgendasTable extends Migration
{
    public function up()
    {
        Schema::create('agendas', function (Blueprint $table) {
            $table->string('agenda_id', 64)->primary();
            $table->string('title', 300)->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('start_at')->nullable();
            $table->bigInteger('end_at')->nullable();
            $table->string('start_iso', 40)->nullable();
            $table->string('end_iso', 40)->nullable();
            $table->string('location', 200)->nullable();
            $table->string('color', 20)->nullable();
            $table->string('created_by', 64)->nullable();
            $table->bigInteger('created_at')->nullable();
            $table->bigInteger('updated_at')->nullable();
            $table->text('thumb_url')->nullable();
            $table->json('meta')->nullable();

            $table->foreign('created_by')->references('uid')->on('users')->onDelete('set null');
            $table->index('start_at');
            $table->index('end_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('agendas');
    }
}

/*
|--------------------------------------------------------------------------
| 13) announcements
|--------------------------------------------------------------------------
*/
class CreateAnnouncementsTable extends Migration
{
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->string('announcement_id', 64)->primary();
            $table->string('title', 200)->nullable();
            $table->text('message')->nullable();
            $table->string('level', 50)->nullable();
            $table->json('days')->nullable();
            $table->json('times')->nullable();
            $table->boolean('tts_enabled')->default(false);
            $table->string('tts_voice', 100)->nullable();
            $table->bigInteger('start_at')->nullable();
            $table->bigInteger('end_at')->nullable();
            $table->string('created_by', 64)->nullable();
            $table->bigInteger('created_at')->nullable();
            $table->json('meta')->nullable();

            $table->foreign('created_by')->references('uid')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('announcements');
    }
}

/*
|--------------------------------------------------------------------------
| 14) notifications (log)
|--------------------------------------------------------------------------
*/
class CreateNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('target_uid', 64)->nullable();
            $table->string('type', 50)->nullable();
            $table->json('payload')->nullable();
            $table->bigInteger('sent_at')->nullable();
            $table->string('status', 50)->nullable();
            $table->bigInteger('created_at')->nullable();

            $table->foreign('target_uid')->references('uid')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
}

/*
|--------------------------------------------------------------------------
| 15) media (uploads)
|--------------------------------------------------------------------------
*/
class CreateMediaTable extends Migration
{
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->string('media_id', 128)->primary();
            $table->string('filename', 300)->nullable();
            $table->text('url')->nullable();
            $table->string('mime', 100)->nullable();
            $table->bigInteger('size_bytes')->nullable();
            $table->string('uploaded_by', 64)->nullable();
            $table->bigInteger('uploaded_at')->nullable();
            $table->json('meta')->nullable();

            $table->foreign('uploaded_by')->references('uid')->on('users')->onDelete('set null');
            $table->index('uploaded_by');
        });
    }

    public function down()
    {
        Schema::dropIfExists('media');
    }
}

/*
|--------------------------------------------------------------------------
| 16) pages (web CMS)
|--------------------------------------------------------------------------
*/
class CreatePagesTable extends Migration
{
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->string('page_id', 128)->primary();
            $table->string('slug', 200)->unique();
            $table->string('title', 300)->nullable();
            $table->text('content_html')->nullable();
            $table->json('content_json')->nullable();
            $table->text('excerpt')->nullable();
            $table->string('author_uid', 64)->nullable();
            $table->bigInteger('published_at')->nullable();
            $table->string('status', 50)->nullable();
            $table->bigInteger('created_at')->nullable();
            $table->bigInteger('updated_at')->nullable();
            $table->json('meta')->nullable();

            $table->foreign('author_uid')->references('uid')->on('users')->onDelete('set null');
        });

        // Postgres tsvector trigger for pages (optional). Only when using Postgres.
        DB::unprepared("
            CREATE FUNCTION pages_tsv_trigger() RETURNS trigger LANGUAGE plpgsql AS $$
            begin
              new.tsv :=
                setweight(to_tsvector('simple', coalesce(new.title,'')), 'A') ||
                setweight(to_tsvector('simple', coalesce(new.excerpt,'')), 'B') ||
                setweight(to_tsvector('simple', coalesce(new.content_html,'')), 'C');
              return new;
            end
            $$;
        ");
        DB::unprepared("
            ALTER TABLE pages ADD COLUMN IF NOT EXISTS tsv tsvector;
            CREATE TRIGGER trg_pages_tsv BEFORE INSERT OR UPDATE ON pages
            FOR EACH ROW EXECUTE FUNCTION pages_tsv_trigger();
        ");
    }

    public function down()
    {
        // drop trigger and function if exist (Postgres)
        DB::unprepared("DROP TRIGGER IF EXISTS trg_pages_tsv ON pages;");
        DB::unprepared("DROP FUNCTION IF EXISTS pages_tsv_trigger();");
        Schema::dropIfExists('pages');
    }
}

/*
|--------------------------------------------------------------------------
| 17) news
|--------------------------------------------------------------------------
*/
class CreateNewsTable extends Migration
{
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->string('news_id', 128)->primary();
            $table->string('title', 300)->nullable();
            $table->string('category', 100)->nullable();
            $table->string('cover_media_id', 128)->nullable();
            $table->text('content_html')->nullable();
            $table->json('content_json')->nullable();
            $table->text('excerpt')->nullable();
            $table->string('slug', 200)->unique()->nullable();
            $table->bigInteger('published_at')->nullable();
            $table->string('created_by', 64)->nullable();
            $table->bigInteger('created_at')->nullable();
            $table->bigInteger('updated_at')->nullable();
            $table->json('meta')->nullable();

            $table->foreign('cover_media_id')->references('media_id')->on('media')->onDelete('set null');
            $table->foreign('created_by')->references('uid')->on('users')->onDelete('set null');
        });

        // Postgres tsvector trigger for news
        DB::unprepared("
            CREATE FUNCTION news_tsv_trigger() RETURNS trigger LANGUAGE plpgsql AS $$
            begin
              new.tsv :=
                setweight(to_tsvector('simple', coalesce(new.title,'')), 'A') ||
                setweight(to_tsvector('simple', coalesce(new.excerpt,'')), 'B') ||
                setweight(to_tsvector('simple', coalesce(new.content_html,'')), 'C');
              return new;
            end
            $$;
        ");
        DB::unprepared("
            ALTER TABLE news ADD COLUMN IF NOT EXISTS tsv tsvector;
            CREATE TRIGGER trg_news_tsv BEFORE INSERT OR UPDATE ON news
            FOR EACH ROW EXECUTE FUNCTION news_tsv_trigger();
        ");
    }

    public function down()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_news_tsv ON news;");
        DB::unprepared("DROP FUNCTION IF EXISTS news_tsv_trigger();");
        Schema::dropIfExists('news');
    }
}

/*
|--------------------------------------------------------------------------
| 18) bills & bill_items
|--------------------------------------------------------------------------
*/
class CreateBillsTable extends Migration
{
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->string('bill_id', 128)->primary();
            $table->string('santri_id', 64)->nullable();
            $table->string('period', 20)->nullable();
            $table->bigInteger('total')->nullable();
            $table->string('status', 50)->nullable();
            $table->json('metadata')->nullable();
            $table->bigInteger('created_at')->nullable();
            $table->bigInteger('due_date')->nullable();

            $table->foreign('santri_id')->references('santri_id')->on('santri')->onDelete('set null');
            $table->index(['santri_id', 'period']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('bills');
    }
}

class CreateBillItemsTable extends Migration
{
    public function up()
    {
        Schema::create('bill_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('bill_id', 128)->nullable();
            $table->string('key_name', 100)->nullable();
            $table->string('title', 200)->nullable();
            $table->bigInteger('amount')->nullable();
            $table->bigInteger('original')->nullable();
            $table->bigInteger('discount')->nullable();
            $table->integer('qty')->default(1);
            $table->json('meta')->nullable();

            $table->foreign('bill_id')->references('bill_id')->on('bills')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bill_items');
    }
}

/*
|--------------------------------------------------------------------------
| 19) faults
|--------------------------------------------------------------------------
*/
class CreateFaultsTable extends Migration
{
    public function up()
    {
        Schema::create('faults', function (Blueprint $table) {
            $table->string('fault_id', 128)->primary();
            $table->string('santri_id', 64)->nullable();
            $table->string('pelapor_uid', 64)->nullable();
            $table->string('title', 300)->nullable();
            $table->text('description')->nullable();
            $table->string('kategori', 100)->nullable();
            $table->integer('poin')->nullable();
            $table->text('tindakan')->nullable();
            $table->string('status', 50)->nullable();
            $table->bigInteger('tanggal')->nullable();
            $table->json('attachments')->nullable();
            $table->bigInteger('created_at')->nullable();

            $table->foreign('santri_id')->references('santri_id')->on('santri')->onDelete('set null');
            $table->foreign('pelapor_uid')->references('uid')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('faults');
    }
}

/*
|--------------------------------------------------------------------------
| 20) izin (permissions/leaves)
|--------------------------------------------------------------------------
*/
class CreateIzinTable extends Migration
{
    public function up()
    {
        Schema::create('izin', function (Blueprint $table) {
            $table->string('izin_id', 128)->primary();
            $table->string('santri_id', 64)->nullable();
            $table->string('requester_uid', 64)->nullable();
            $table->text('reason')->nullable();
            $table->text('note')->nullable();
            $table->bigInteger('requested_at')->nullable();
            $table->bigInteger('planned_out_at')->nullable();
            $table->bigInteger('planned_return_at')->nullable();
            $table->bigInteger('actual_return_at')->nullable();
            $table->string('status', 50)->nullable();
            $table->json('penjemput')->nullable();
            $table->json('meta')->nullable();

            $table->foreign('santri_id')->references('santri_id')->on('santri')->onDelete('set null');
            $table->foreign('requester_uid')->references('uid')->on('users')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('izin');
    }
}

/*
|--------------------------------------------------------------------------
| 21) kunjungan (visits)
|--------------------------------------------------------------------------
*/
class CreateKunjunganTable extends Migration
{
    public function up()
    {
        Schema::create('kunjungan', function (Blueprint $table) {
            $table->string('visit_id', 128)->primary();
            $table->string('santri_id', 64)->nullable();
            $table->string('visitor_name', 200)->nullable();
            $table->string('relation', 100)->nullable();
            $table->bigInteger('arrived_at')->nullable();
            $table->bigInteger('left_at')->nullable();
            $table->text('purpose')->nullable();
            $table->bigInteger('created_at')->nullable();
            $table->json('meta')->nullable();

            $table->foreign('santri_id')->references('santri_id')->on('santri')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('kunjungan');
    }
}

/*
|--------------------------------------------------------------------------
| 22) audit_logs
|--------------------------------------------------------------------------
*/
class CreateAuditLogsTable extends Migration
{
    public function up()
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uid', 64)->nullable();
            $table->string('action', 200)->nullable();
            $table->string('entity_type', 100)->nullable();
            $table->string('entity_id', 128)->nullable();
            $table->json('payload')->nullable();
            $table->bigInteger('created_at')->default(DB::raw('(EXTRACT(EPOCH FROM now()) * 1000)::BIGINT'));
            $table->index(['entity_type', 'entity_id']);
            $table->index('uid');
        });
    }

    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }
}

/*
|--------------------------------------------------------------------------
| 23) settings (key-value)
|--------------------------------------------------------------------------
*/
class CreateSettingsTable extends Migration
{
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->text('key')->primary();
            $table->json('value')->nullable();
            $table->bigInteger('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('settings');
    }
}

/*
|--------------------------------------------------------------------------
| Optional: materialized view / helper (no direct Schema builder)
|--------------------------------------------------------------------------
| If you want the materialized view `mv_latest_attendance`, run a raw SQL
| migration after attendance_records is created. Example below:
*/
class CreateMaterializedViews extends Migration
{
    public function up()
    {
        // Only for Postgres
        DB::unprepared("
            CREATE MATERIALIZED VIEW IF NOT EXISTS mv_latest_attendance AS
            SELECT DISTINCT ON (ar.santri_id) ar.santri_id, ar.ts, ar.status, ar.session_key, ar.name
            FROM attendance_records ar
            ORDER BY ar.santri_id, ar.ts DESC;
        ");
    }

    public function down()
    {
        DB::unprepared("DROP MATERIALIZED VIEW IF EXISTS mv_latest_attendance;");
    }
}s
