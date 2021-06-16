<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Rfc4122\UuidV4;

class InitialSchema extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->enum('role', ['repo_manager', 'team_member'])->default('repo_manager');
            $table->string('profile_picture_url')->nullable();
            $table->string('ui_language')->default('en');
            $table->string('ui_language_display_format')->default('endonym');
            $table->string('ui_date_display_format')->default('YY-MM-DD');
        });

        Schema::create('repository_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('user_repositories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('type_id')
                ->constrained('repository_types')
                ->onDelete('cascade');
            $table->string('name');
            $table->string('api_endpoint');
            $table->string('client_secret')->nullable();
            $table->boolean('connection_verified')->default(false);
            $table->timestamps();
        });

        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')
                ->constrained('teams')
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('team_id')
                ->constrained('teams')
                ->onDelete('cascade');
            $table->string('email');
            $table->uuid('invitation_code')->default(UuidV4::uuid4());
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();

            // Indexes
            $table->index('email');
            $table->index('invitation_code');
        });

        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')
                ->constrained('teams')
                ->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('inherit_information_to_resources')->default(false);
            $table->boolean('keywords_extracted_from_resources')->default(true);
            $table->boolean('publish_as_catalogue_of_resources')->default(false);
            $table->string('doi')->nullable();
            $table->string('publisher')->nullable();
            $table->timestamp('embargo_date')->nullable();
            $table->boolean('geospatial_coverage_calculated_from_resources')->default(false);
            $table->boolean('temporal_coverage_calculated_from_resources')->default(false);
            $table->float('findable_score')->default(0);
            $table->float('accessible_score')->default(0);
            $table->float('interoperable_score')->default(0);
            $table->float('reusable_score')->default(0);
            $table->float('fair_scoring')->default(0);
            $table->timestamps();
        });

        Schema::create('collection_keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')
                ->constrained('collections')
                ->onDelete('cascade');
            $table->string('keyword');
            $table->timestamps();
        });

        Schema::create('collection_geospatial_coverages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')
                ->constrained('collections')
                ->onDelete('cascade');
            $table->string('country');
            $table->timestamps();
        });

        Schema::create('collection_temporal_coverages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')
                ->constrained('collections')
                ->onDelete('cascade');
            $table->enum('type', ['period', 'timepoint']);
            $table->text('description')->nullable();
            $table->timestamp('from_date')->nullable();
            $table->timestamp('to_date')->nullable();
            $table->timestamps();
        });

        // TODO: Finish the table structure.
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->enum('status', [
                'draft',
                'under_preparation',
                'under_review',
                'approved',
                'published'
            ])->default('draft');
            $table->float('findable_score')->default(0);
            $table->float('accessible_score')->default(0);
            $table->float('interoperable_score')->default(0);
            $table->float('reusable_score')->default(0);
            $table->float('fair_scoring')->default(0);
            // TODO: Maybe include information regarding who published the resource?
            $table->timestamp('published_at')->nullable()->default(null);
            $table->timestamps();
        });

        Schema::create('collection_resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('collection_id')
                ->constrained('collections')
                ->onDelete('cascade');
            $table->foreignId('resource_id')
                ->constrained('resources')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name');
            $table->dropColumn([
                'firstname',
                'lastname',
                'role',
                'profile_picture_url',
                'ui_language',
                'ui_language_display_format',
                'ui_date_display_format',
            ]);
        });
        Schema::drop('user_repositories');
        Schema::drop('collection_keywords');
        Schema::drop('collection_geospatial_coverages');
        Schema::drop('collection_temporal_coverages');
        Schema::drop('invitations');
        Schema::drop('team_members');
        Schema::drop('collection_resources');
        Schema::drop('collections');
        Schema::drop('teams');
        Schema::drop('resources');
        Schema::drop('repository_types');
    }
}
