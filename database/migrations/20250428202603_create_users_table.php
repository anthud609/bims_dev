<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
{
    public function change(): void
    {
        // Define table with no auto-id (we’ll add our own char(36) PK)
        $table = $this->table('users1', [
            'id'          => false,
            'primary_key' => ['id'],
            'engine'      => 'InnoDB',
            'encoding'    => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ]);

        $table
            ->addColumn('id',              'char',      ['limit' => 36])
            ->addColumn('email',           'string',    ['limit' => 255])
            ->addColumn('password',        'string',    ['limit' => 255])
            ->addColumn('created_at',      'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at',      'timestamp', [
                'default' => 'CURRENT_TIMESTAMP',
                'update'  => 'CURRENT_TIMESTAMP',
            ])
            ->addColumn('failed_attempts', 'integer',   ['signed' => false, 'default' => 0])
            ->addColumn('last_failed_at',  'timestamp', ['null' => true, 'default' => null])
            ->addColumn('is_locked',       'boolean',   ['default' => false])
            ->addIndex(['email'], ['unique' => true])
            ->create();
    }
}
