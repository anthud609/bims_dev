<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateEmployeesTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('employees', [
            'id'          => false,
            'primary_key' => ['id'],
            'engine'      => 'InnoDB',
            'encoding'    => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ]);

        $table
            ->addColumn('id',         'char',   ['limit' => 36])
            ->addColumn('user_id',    'char',   [
                'limit'   => 36,
                'null'    => true,
                'default' => null,
            ])
            ->addColumn('first_name', 'string', ['limit' => 100])
            ->addColumn('last_name',  'string', ['limit' => 100])
            ->addColumn('title',      'string', [
                'limit'   => 100,
                'null'    => true,
                'default' => null,
            ])
            ->addColumn('created_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
            ])
            ->addColumn('updated_at', 'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'update'  => 'CURRENT_TIMESTAMP',
            ])
            ->addIndex(['user_id'], [
                'unique' => true,
                'name'   => 'uniq_employees_user_id',
            ])
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                [
                    'delete' => 'SET_NULL',
                    'update' => 'NO_ACTION',
                ],
                'fk_employees_user'
            )
            ->create();
    }
}
