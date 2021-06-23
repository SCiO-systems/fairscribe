<?php

namespace Database\Seeders;

use App\Models\Collection;
use App\Models\Invite;
use App\Models\Resource;
use App\Models\Team;
use App\Models\User;
use Database\Factories\TeamFactory;
use DB;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Container\Container;
use Schema;

class InitialSeeder extends Seeder
{
    /**
     * The current Faker instance.
     *
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * Create a new seeder instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->faker = $this->withFaker();
    }

    /**
     * Get a new Faker instance.
     *
     * @return \Faker\Generator
     */
    protected function withFaker()
    {
        return Container::getInstance()->make(Generator::class);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        DB::table('teams')->truncate();
        DB::table('team_user')->truncate();
        DB::table('collections')->truncate();
        DB::table('resources')->truncate();
        DB::table('collection_resource')->truncate();
        DB::table('invites')->truncate();
        DB::disableQueryLog();

        $email = 'datascribe@scio.systems';
        $password = 'scio';

        // Create the main user.
        $user = User::create([
            'firstname' => 'Scio',
            'lastname' => 'Systems',
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        $users = User::factory()->count(10)->create();

        // Resources.
        $resources = Resource::factory(['author_id' => $user->id])
            ->count(50)
            ->create();

        // Create teams.
        $teams = Team::factory(['owner_id' => $user->id])
            ->count(20)
            ->create()
            ->each(function ($team) use ($resources) {
                // Create collections and associate with resources.
                Collection::factory(['team_id' => $team->id])
                    ->count(20)
                    ->create()->each(function ($collection) use ($resources) {
                        $collection->resources()->attach($resources);
                    });
            });

        $ownerId = 2;
        $sharedTeams = Team::factory(['owner_id' => $ownerId])
            ->count(10)->create()->each(
                function ($team) use ($user, $email) {
                    $team->users()->attach($user);

                    // Create the invites as well.
                    $invites = Invite::factory(['team_id' => $team->id, 'email' => $email])
                        ->create();
                }
            );

        Schema::enableForeignKeyConstraints();
    }
}
