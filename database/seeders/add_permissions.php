<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class add_permissions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'create projects', 'ru_name' => 'Создать проект']);
        Permission::create(['name' => 'remove projects', 'ru_name' => 'Удалить проект']);
        Permission::create(['name' => 'edit projects', 'ru_name' => 'Редактировать проект']);

        Permission::create(['name' => 'pay invoices', 'ru_name' => 'Оплатить счет']);
        Permission::create(['name' => 'agree invoices', 'ru_name' => 'Согласовать счет на оплату']);
        Permission::create(['name' => 'remove invoices', 'ru_name' => 'Удалить счет']);
        Permission::create(['name' => 'create invoices', 'ru_name' => 'Создать инвойс']);

        Permission::create(['name' => 'remove clients', 'ru_name' => 'Удалить клиента']);
        Permission::create(['name' => 'remove suppliers', 'ru_name' => 'Удалить поставщика']);

        Permission::create(['name' => 'add project plan', 'ru_name' => 'Создать план проекта']);
        Permission::create(['name' => 'edit project plan', 'ru_name' => 'Редактировать план проекта']);
        Permission::create(['name' => 'work with projects', 'ru_name' => 'Работать с проектом']);

        Permission::create(['name' => 'add users', 'ru_name' => 'Добавить пользователя']);
        Permission::create(['name' => 'edit users', 'ru_name' => 'Редактировать пользователя']);
        Permission::create(['name' => 'remove users', 'ru_name' => 'Удалить пользователя']);

        Permission::create(['name' => 'remove containers', 'ru_name' => 'Удалить контейнер']);

        // create roles and assign existing permissions
        $role1 = Role::create(['name' => 'manager', 'ru_name' => 'Менеджер']);
        $role1->givePermissionTo('create projects');
        $role1->givePermissionTo('edit projects');
        $role1->givePermissionTo('work with projects');
        $role1->givePermissionTo('add project plan');
        $role1->givePermissionTo('edit project plan');
        $role1->givePermissionTo('remove containers');

        $role5 = Role::create(['name' => 'logist', 'ru_name' => 'Логист']);
        $role5->givePermissionTo('create projects');
        $role5->givePermissionTo('edit projects');
        $role5->givePermissionTo('work with projects');
        $role5->givePermissionTo('add project plan');
        $role5->givePermissionTo('edit project plan');
        $role5->givePermissionTo('remove containers');

        $role2 = Role::create(['name' => 'accountant', 'ru_name' => 'Бухгалтер']);
        $role2->givePermissionTo('pay invoices');

        $role3 = Role::create(['name' => 'director', 'ru_name' => 'Директор']);
        $role3->givePermissionTo('create projects');
        $role3->givePermissionTo('remove projects');
        $role3->givePermissionTo('edit projects');
        $role3->givePermissionTo('remove invoices');
        $role3->givePermissionTo('remove clients');
        $role3->givePermissionTo('remove suppliers');
        $role3->givePermissionTo('add users');
        $role3->givePermissionTo('edit users');
        $role3->givePermissionTo('remove users');
        $role3->givePermissionTo('remove containers');

        Role::create(['name' => 'user', 'ru_name' => 'Без доступа']);
        $role4 = Role::create(['name' => 'super-admin', 'ru_name' => 'Админ']);
        Role::create(['name' => 'special', 'ru_name' => 'Настраиваемый']);

        // create demo users
        $user = \App\Models\User::factory()->create([
            'name' => 'Ава Liu',
            'position' => 'Ген Помощник',
            'email' => 'ava@rocklogistic.ru',
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole($role3);

        $user = \App\Models\User::factory()->create([
            'name' => 'Оля Сяо',
            'position' => 'Помощник Учредителя',
            'email' => 'xiao@rocklogistic.ru',
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole($role3);

        $user = \App\Models\User::factory()->create([
            'name' => 'Илья Хохлов',
            'position' => 'Генеральный директор',
            'email' => 'khokhlov@rocklogistic.ru',
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole($role3);

        $user = \App\Models\User::factory()->create([
            'name' => 'Володя 常',
            'position' => 'Менеджер по клиентам',
            'email' => 'chang@rocklogistic.ru',
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole($role1);

        $user = \App\Models\User::factory()->create([
            'name' => 'Мария Ни',
            'position' => 'Помощник',
            'email' => 'nie@rocklogistic.ru',
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole($role1);

        $user = \App\Models\User::factory()->create([
            'name' => 'Екатерина Карлова',
            'position' => 'Ведущий логист',
            'email' => 'karlova@rocklogistic.ru',
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole($role5);

        $user = \App\Models\User::factory()->create([
            'name' => 'Татьяна Черкашина',
            'position' => 'Главный бухгалтер',
            'email' => 'buh@rocklogistic.ru',
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole($role2);

        $user = \App\Models\User::factory()->create([
            'name' => 'Чжао Хоуфу',
            'position' => 'Учредитель',
            'email' => 'zhao@rocklogistic.ru',
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole($role3);

        $user = \App\Models\User::factory()->create([
            'name' => 'Данил Зубарев',
            'position' => 'Главный администратор',
            'email' => 'danilzubarev@gmail.com',
            'password' => Hash::make('137946'),
        ]);
        $user->assignRole($role4);

        $user = \App\Models\User::factory()->create([
            'name' => 'Эльвира Ревякина',
            'position' => 'Переводчик',
            'email' => 'revyakina@rocklogistic.ru',
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole($role1);

        $user = \App\Models\User::factory()->create([
            'name' => 'Лилия Шевченко',
            'position' => 'Ведущий бухгалтер',
            'email' => 'shevchenko@rocklogistic.ru',
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole($role2);

        $user = \App\Models\User::factory()->create([
            'name' => 'Семён Шляхтов',
            'position' => 'Снабжение',
            'email' => 'shlyakhtov@rocklogistic.ru',
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole($role5);

        $user = \App\Models\User::factory()->create([
            'name' => 'Мария Долгих',
            'position' => 'Ведущий логист по авто',
            'email' => 'dolgikh@rocklogistic.ru',
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole($role5);

        $user = \App\Models\User::factory()->create([
            'name' => 'Юрий Ильин',
            'position' => 'Оператор по авто',
            'email' => 'auto@rocklogistic.ru',
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole($role5);

        $user = \App\Models\User::factory()->create([
            'name' => 'Анастасия Одинцова',
            'position' => 'Младший логист',
            'email' => 'odintsova@rocklogistic.ru',
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole($role5);

        $user = \App\Models\User::factory()->create([
            'name' => 'Светлана Баженова',
            'position' => 'Логист',
            'email' => 'bazhenova@rocklogistic.ru',
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole($role5);

        $user = \App\Models\User::factory()->create([
            'name' => 'Наталья Дащинская',
            'position' => 'Бухгалтер',
            'email' => 'daschinskaya@rocklogistic.ru',
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole($role2);

        $user = \App\Models\User::factory()->create([
            'name' => 'Анатолий Шутко',
            'position' => 'Юрист',
            'email' => 'anatoli@rocklogistic.ru',
            'password' => Hash::make('123456'),
        ]);
        $user->assignRole($role1);
    }
}
