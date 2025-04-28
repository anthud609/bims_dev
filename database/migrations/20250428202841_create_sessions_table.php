<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSessionsTable extends AbstractMigration
{
    public function change(): void
    {
        // Define table without default auto-increment id
        $table = $this->table('sessions', [
            'id'          => false,
            'primary_key' => ['id'],
            'engine'      => 'InnoDB',
            'encoding'    => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ]);

        $table
            ->addColumn('id',            'char',      ['limit' => 36])
            ->addColumn('user_id',       'char',      ['limit' => 36])
            ->addColumn('token',         'char',      ['limit' => 64])
            ->addColumn('ip_address',    'string',    ['limit' => 45,  'null' => true,  'default' => null])
            ->addColumn('user_agent',    'string',    ['limit' => 255, 'null' => true,  'default' => null])
            ->addColumn('created_at',    'datetime',  ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('last_activity', 'datetime',  ['null'    => true,   'default' => null])
            ->addColumn('expires_at',    'datetime',  ['null'    => false])
            ->addColumn('is_revoked',    'boolean',   ['default' => false])
            ->addIndex(['token'],        ['unique' => true, 'name' => 'uniq_sessions_token'])
            ->addIndex(['user_id'],      ['name'   => 'idx_sessions_user'])
            // Phinx automatically deduplicates identical indexes, so you don’t need a second token index
            ->create();
    }
}
