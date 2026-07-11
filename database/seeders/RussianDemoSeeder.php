<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Models\Organization;
use App\Models\Project;
use App\Models\Templates;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RussianDemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $users = $this->seedUsers();
            $projects = $this->seedOrganizationsAndProjects($users);

            $this->seedTemplates($projects);
        });

        $this->command?->info('Russian demo data has been added.');
    }

    /**
     * @return array<string, User>
     */
    private function seedUsers(): array
    {
        $password = '1234567';
        $users = [
            'admin' => [
                'name' => 'Системный администратор',
                'login' => 'demo_admin',
                'role' => UserRole::Admin->value,
                'description' => 'Супер администратор для проверки полного доступа ко всем разделам.',
            ],
            'organization_admin' => [
                'name' => 'Администратор организации',
                'login' => 'demo_org_admin',
                'role' => UserRole::OrganizationAdmin->value,
                'description' => 'Управляет назначенными организациями и всеми проектами внутри них.',
            ],
            'project_admin' => [
                'name' => 'Администратор проекта',
                'login' => 'demo_project_admin',
                'role' => UserRole::ProjectAdmin->value,
                'description' => 'Управляет назначенными проектами и связанными с ними данными.',
            ],
            'moderator' => [
                'name' => 'Модератор проекта',
                'login' => 'demo_moderator',
                'role' => UserRole::Moderator->value,
                'description' => 'Работает с подписчиками в назначенных проектах.',
            ],
        ];

        foreach ($users as $key => $data) {
            $users[$key] = User::query()->updateOrCreate(
                ['login' => $data['login']],
                $data + ['password' => $password]
            );
        }

        return $users;
    }

    /**
     * @param array<string, User> $users
     * @return array<int, Project>
     */
    private function seedOrganizationsAndProjects(array $users): array
    {
        $organizations = [
            [
                'name' => 'ООО Северные письма',
                'description' => 'Организация для проверки клиентских рассылок и сегментации аудитории.',
                'projects' => [
                    'Новости клиентов',
                    'Программа лояльности',
                ],
            ],
            [
                'name' => 'АО Маркетинговая лаборатория',
                'description' => 'Организация для тестирования образовательных и продуктовых кампаний.',
                'projects' => [
                    'Корпоративные рассылки',
                    'Обучающие вебинары',
                    'Дайджест продукта',
                ],
            ],
            [
                'name' => 'ИП Городские рассылки',
                'description' => 'Организация для локальных событий, акций и сервисных уведомлений.',
                'projects' => [
                    'Городские события',
                    'Акции партнеров',
                    'Онбординг подписчиков',
                    'Реактивация базы',
                    'Сервисные уведомления',
                ],
            ],
        ];

        $projects = [];

        foreach ($organizations as $organizationIndex => $organizationData) {
            $organization = Organization::query()->updateOrCreate(
                ['name' => $organizationData['name']],
                [
                    'owner_id' => $users['organization_admin']->id,
                    'description' => $organizationData['description'],
                ]
            );

            $this->upsertOrganizationAdministrator($organization, $users['organization_admin']);

            foreach ($organizationData['projects'] as $projectIndex => $projectName) {
                $project = Project::query()->updateOrCreate(
                    [
                        'organization_id' => $organization->id,
                        'name' => $projectName,
                    ],
                    [
                        'description' => 'Тестовый проект организации "' . $organization->name . '" для русскоязычных рассылок.',
                        'status' => ProjectStatus::Active->value,
                        'default_sender_name' => $organization->name,
                        'default_from_email' => sprintf(
                            'project-%d-%d@example.test',
                            $organizationIndex + 1,
                            $projectIndex + 1
                        ),
                        'default_reply_to' => sprintf(
                            'reply-%d-%d@example.test',
                            $organizationIndex + 1,
                            $projectIndex + 1
                        ),
                        'timezone' => 'Europe/Moscow',
                    ]
                );

                $this->upsertProjectUser($project, $users['project_admin'], UserRole::ProjectAdmin);
                $this->upsertProjectUser($project, $users['moderator'], UserRole::Moderator);

                $projects[] = $project;
            }
        }

        return $projects;
    }

    /**
     * @param array<int, Project> $projects
     */
    private function seedTemplates(array $projects): void
    {
        $templates = [
            [
                'name' => 'Приветственное письмо для новых подписчиков',
                'prior' => 0,
                'body' => '<h2>Здравствуйте, {{name}}!</h2><p>Спасибо за подписку. В этом письме собраны первые материалы, которые помогут быстро познакомиться с проектом.</p>',
            ],
            [
                'name' => 'Июльский дайджест продукта',
                'prior' => 0,
                'body' => '<h2>Главные новости месяца</h2><p>Мы подготовили обзор новых функций, полезных материалов и ближайших планов развития продукта.</p>',
            ],
            [
                'name' => 'Приглашение на вебинар',
                'prior' => 1,
                'body' => '<h2>Приглашаем на вебинар</h2><p>Расскажем, как сегментировать базу подписчиков, готовить рассылки и анализировать результаты отправки.</p>',
            ],
            [
                'name' => 'Летняя акция для клиентов',
                'prior' => 1,
                'body' => '<h2>Специальное предложение</h2><p>Только на этой неделе действует персональная скидка для активных подписчиков.</p>',
            ],
            [
                'name' => 'Реактивация неактивных подписчиков',
                'prior' => 2,
                'body' => '<h2>Мы давно не виделись</h2><p>Подготовили короткую подборку полезных материалов и предложений, которые могут быть вам интересны.</p>',
            ],
        ];

        foreach ($templates as $index => $template) {
            $project = $projects[$index] ?? $projects[0] ?? null;

            if (!$project) {
                continue;
            }

            Templates::query()->updateOrCreate(
                ['name' => $template['name']],
                [
                    'project_id' => $project->id,
                    'body' => $template['body'],
                    'prior' => $template['prior'],
                ]
            );
        }
    }

    private function upsertOrganizationAdministrator(Organization $organization, User $user): void
    {
        DB::table('organization_admins')->updateOrInsert(
            [
                'organization_id' => $organization->id,
                'user_id' => $user->id,
            ],
            [
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function upsertProjectUser(Project $project, User $user, UserRole $role): void
    {
        DB::table('project_admins')->updateOrInsert(
            [
                'project_id' => $project->id,
                'user_id' => $user->id,
            ],
            [
                'role' => $role->value,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
