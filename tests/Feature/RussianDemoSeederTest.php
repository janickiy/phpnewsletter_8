<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Subscribers;
use App\Models\Templates;
use App\Models\User;
use Database\Seeders\RussianDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use ReflectionMethod;
use Tests\TestCase;

class RussianDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var array<int, string>
     */
    private array $organizationNames = [
        'ООО Северные письма',
        'АО Маркетинговая лаборатория',
        'ИП Городские рассылки',
    ];

    /**
     * @var array<int, string>
     */
    private array $templateNames = [
        'Приветственное письмо для новых подписчиков',
        'Июльский дайджест продукта',
        'Приглашение на вебинар',
        'Летняя акция для клиентов',
        'Реактивация неактивных подписчиков',
    ];

    public function test_it_seeds_russian_demo_data_and_all_required_relations(): void
    {
        $this->seed(RussianDemoSeeder::class);

        $this->assertSeededUsers();
        $this->assertSeededOrganizationsAndProjects();
        $this->assertSeededTemplates();
        $this->assertSeededSubscribers();
    }

    public function test_it_can_be_run_repeatedly_without_creating_duplicates(): void
    {
        $this->seed(RussianDemoSeeder::class);
        $firstMetrics = $this->demoMetrics();

        $this->seed(RussianDemoSeeder::class);

        $this->assertSame($firstMetrics, $this->demoMetrics());
    }

    public function test_russian_subscriber_name_uses_cyrillic_male_and_female_forms(): void
    {
        $method = new ReflectionMethod(RussianDemoSeeder::class, 'russianSubscriberName');
        $seeder = new RussianDemoSeeder();

        $firstNames = [
            'Александр',
            'Дмитрий',
            'Максим',
            'Иван',
            'Артем',
            'Сергей',
            'Андрей',
            'Никита',
            'Михаил',
            'Егор',
            'Анна',
            'Мария',
            'Елена',
            'Ольга',
            'Наталья',
            'Ирина',
            'Светлана',
            'Татьяна',
            'Виктория',
            'Дарья',
        ];
        $lastNames = [
            'Иванов',
            'Петров',
            'Смирнов',
            'Кузнецов',
            'Попов',
            'Васильев',
            'Соколов',
            'Михайлов',
            'Новиков',
            'Федоров',
            'Морозов',
            'Волков',
            'Алексеев',
            'Лебедев',
            'Семенов',
            'Егоров',
            'Павлов',
            'Козлов',
            'Степанов',
            'Николаев',
        ];
        $patronymics = [
            'Александрович',
            'Дмитриевич',
            'Максимович',
            'Иванович',
            'Сергеевич',
            'Андреевич',
            'Михайлович',
            'Егорович',
            'Александровна',
            'Дмитриевна',
            'Максимовна',
            'Ивановна',
            'Сергеевна',
            'Андреевна',
            'Михайловна',
            'Егоровна',
        ];

        $this->assertSame(
            'Иванов Александр Александрович',
            $method->invoke($seeder, 1, $firstNames, $lastNames, $patronymics)
        );
        $this->assertSame(
            'Иванова Анна Максимовна',
            $method->invoke($seeder, 11, $firstNames, $lastNames, $patronymics)
        );
    }

    private function assertSeededUsers(): void
    {
        $this->assertDatabaseHas('users', [
            'login' => 'demo_admin',
            'role' => UserRole::Admin->value,
        ]);
        $this->assertDatabaseHas('users', [
            'login' => 'demo_org_admin',
            'role' => UserRole::OrganizationAdmin->value,
        ]);
        $this->assertDatabaseHas('users', [
            'login' => 'demo_project_admin',
            'role' => UserRole::ProjectAdmin->value,
        ]);
        $this->assertDatabaseHas('users', [
            'login' => 'demo_moderator',
            'role' => UserRole::Moderator->value,
        ]);

        $this->assertSame(4, User::query()->whereIn('login', $this->demoUserLogins())->count());
    }

    private function assertSeededOrganizationsAndProjects(): void
    {
        $organizationAdmin = User::query()->where('login', 'demo_org_admin')->firstOrFail();
        $projectAdmin = User::query()->where('login', 'demo_project_admin')->firstOrFail();
        $moderator = User::query()->where('login', 'demo_moderator')->firstOrFail();
        $organizations = Organization::query()
            ->whereIn('name', $this->organizationNames)
            ->withCount('projects')
            ->get()
            ->keyBy('name');

        $this->assertCount(3, $organizations);
        $this->assertSame(2, $organizations['ООО Северные письма']->projects_count);
        $this->assertSame(3, $organizations['АО Маркетинговая лаборатория']->projects_count);
        $this->assertSame(5, $organizations['ИП Городские рассылки']->projects_count);

        foreach ($organizations as $organization) {
            $this->assertSame($organizationAdmin->id, $organization->owner_id);
            $this->assertDatabaseHas('organization_admins', [
                'organization_id' => $organization->id,
                'user_id' => $organizationAdmin->id,
            ]);
        }

        $projects = Project::query()
            ->whereIn('organization_id', $organizations->pluck('id'))
            ->get();

        $this->assertCount(10, $projects);

        foreach ($projects as $project) {
            $this->assertDatabaseHas('project_admins', [
                'project_id' => $project->id,
                'user_id' => $projectAdmin->id,
                'role' => UserRole::ProjectAdmin->value,
            ]);
            $this->assertDatabaseHas('project_admins', [
                'project_id' => $project->id,
                'user_id' => $moderator->id,
                'role' => UserRole::Moderator->value,
            ]);
        }
    }

    private function assertSeededTemplates(): void
    {
        $templates = Templates::query()->whereIn('name', $this->templateNames)->get();

        $this->assertCount(5, $templates);
        $this->assertTrue($templates->every(fn (Templates $template): bool => !is_null($template->project_id)));
        $this->assertTrue($templates->every(fn (Templates $template): bool => preg_match('/\p{Cyrillic}/u', $template->name) === 1));
    }

    private function assertSeededSubscribers(): void
    {
        $subscribers = Subscribers::query()
            ->where('email', 'like', 'ru.subscriber%@phpnewsletter.test')
            ->get();

        $this->assertCount(200, $subscribers);
        $this->assertTrue($subscribers->every(
            fn (Subscribers $subscriber): bool => preg_match('/\p{Cyrillic}/u', (string) $subscriber->name) === 1
        ));
        $this->assertSame(240, $this->demoProjectSubscriberLinks());
        $this->assertSame(450, $this->demoCategoryLinks());
    }

    /**
     * @return array<string, int>
     */
    private function demoMetrics(): array
    {
        return [
            'users' => User::query()->whereIn('login', $this->demoUserLogins())->count(),
            'organizations' => Organization::query()->whereIn('name', $this->organizationNames)->count(),
            'projects' => Project::query()
                ->whereIn(
                    'organization_id',
                    Organization::query()->whereIn('name', $this->organizationNames)->pluck('id')
                )
                ->count(),
            'templates' => Templates::query()->whereIn('name', $this->templateNames)->count(),
            'subscribers' => Subscribers::query()->where('email', 'like', 'ru.subscriber%@phpnewsletter.test')->count(),
            'project_subscriber_links' => $this->demoProjectSubscriberLinks(),
            'category_links' => $this->demoCategoryLinks(),
        ];
    }

    /**
     * @return array<int, string>
     */
    private function demoUserLogins(): array
    {
        return [
            'demo_admin',
            'demo_org_admin',
            'demo_project_admin',
            'demo_moderator',
        ];
    }

    private function demoProjectSubscriberLinks(): int
    {
        return DB::table('project_subscriber')
            ->join('subscribers', 'subscribers.id', '=', 'project_subscriber.subscriber_id')
            ->where('subscribers.email', 'like', 'ru.subscriber%@phpnewsletter.test')
            ->count();
    }

    private function demoCategoryLinks(): int
    {
        return DB::table('subscriptions')
            ->join('subscribers', 'subscribers.id', '=', 'subscriptions.subscriber_id')
            ->where('subscribers.email', 'like', 'ru.subscriber%@phpnewsletter.test')
            ->count();
    }
}
