<?php

declare(strict_types=1);

namespace GiacomoMasseroni\LaravelModelsGenerator\Contracts;

use Doctrine\DBAL\Schema\Column;
use GiacomoMasseroni\LaravelModelsGenerator\Entities\Entity;
use GiacomoMasseroni\LaravelModelsGenerator\Entities\Table;
use GiacomoMasseroni\LaravelModelsGenerator\Entities\View;

interface DriverConnectorInterface
{
    /**
     * @return array{
     *     'driver': 'ibm_db2'|'mysqli'|'oci8'|'pdo_mysql'|'pdo_oci'|'pdo_pgsql'|'pdo_sqlite'|'pdo_sqlsrv'|'pgsql'|'sqlite3'|'sqlsrv'
     * }
     */
    public function connectionParams(): array;

    /**
     * @return array<string, Table>
     */
    public function listTables(): array;

    /**
     * @return array<string, View>
     */
    public function listViews(): array;

    /**
     * @return array<string, mixed>
     */
    public function getEntityColumns(string $entityName): array;

    /**
     * @return array<string, mixed>
     */
    public function getEntityIndexes(string $entityName): array;

    public function laravelColumnTypeForCast(Column $column, \Doctrine\DBAL\Schema\Table $table, ?Entity $dbTable = null): string;

    public function laravelColumnType(Column $column, \Doctrine\DBAL\Schema\Table $table, ?Entity $dbTable = null): string;
}
