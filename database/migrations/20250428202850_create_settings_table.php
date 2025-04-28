<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSettingsTable extends AbstractMigration
{
    public function change(): void
    {
        // We’ll declare our own unsigned auto-inc id
        $table = $this->table('settings', [
            'id'          => false,
            'primary_key' => ['id'],
            'engine'      => 'InnoDB',
            'encoding'    => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ]);

        $table
            // recreate `id INT(10) unsigned NOT NULL AUTO_INCREMENT`
            ->addColumn('id',    'integer', [
                'signed'   => false,
                'identity' => true,
            ])
            ->addColumn('key',   'string',  [
                'limit'    => 191,
            ])
            ->addColumn('value', 'text',    [])
            ->addIndex(['key'], [
                'unique' => true,
                'name'   => 'uniq_settings_key',
            ])
            ->create();
    }
}
