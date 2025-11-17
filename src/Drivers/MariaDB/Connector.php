<?php

declare(strict_types=1);

namespace GiacomoMasseroni\LaravelModelsGenerator\Drivers\MariaDB;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;
use GiacomoMasseroni\LaravelModelsGenerator\Concerns\DBALable;
use GiacomoMasseroni\LaravelModelsGenerator\Contracts\DriverConnectorInterface;
use GiacomoMasseroni\LaravelModelsGenerator\Drivers\DriverConnector;
use GiacomoMasseroni\LaravelModelsGenerator\Entities\Property;
use GiacomoMasseroni\LaravelModelsGenerator\Entities\View;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Doctrine\UuidType;

class Connector extends DriverConnector implements DriverConnectorInterface
{
    use DBALable;

    /**
     * @throws Exception
     */
    public function __construct(?string $connection = null, ?string $schema = null, ?string $table = null)
    {
        parent::__construct($connection, $schema, $table);

        $this->conn = DriverManager::getConnection($this->connectionParams());

        // Add Uuid ramsey type in doctrine for uuid column
        Type::addType('uuid', UuidType::class);

        $platform = $this->conn->getDatabasePlatform();
        $platform->registerDoctrineTypeMapping('enum', 'string');
        $this->sm = $this->conn->createSchemaManager();
    }

    public function connectionParams(): array
    {
        /** @phpstan-ignore-next-line */
        return [
            'dbname' => $this->schema,
            'user' => (string) config('database.connections.'.config('database.default').'.username'),
            'password' => (string) config('database.connections.'.config('database.default').'.password'),
            'host' => (string) config('database.connections.'.config('database.default').'.host'),
            'port' => (string) config('database.connections.'.config('database.default').'.port'),
            'driver' => 'pdo_mysql',
        ];
    }

    private function getView(string $viewName): View
    {
        $columns = $this->getEntityColumns($viewName);
        $properties = [];

        $dbView = new View($viewName, dbEntityNameToModelName($viewName));
        $dbView->fillable = array_diff(
            array_keys($columns),
            ['created_at', 'updated_at', 'deleted_at']
        );

        /** @var Column $column */
        foreach ($columns as $column) {
            $laravelColumnType = $this->laravelColumnType($column, null, $dbView);
            $dbView->casts[$column->getName()] = $this->laravelColumnTypeForCast($column, null, $dbView);

            $properties[] = new Property(
                '$'.$column->getName(),
                ($this->typeColumnPropertyMaps[$laravelColumnType] ?? $laravelColumnType).($column->getNotnull() ? '' : '|null'),
                true
            );
        }
        $dbView->properties = $properties;

        return $dbView;
    }

    public function listViews(): array
    {
        /** @var array<string, View> $dbViews */
        $dbViews = [];

        $sql = "SHOW FULL TABLES IN $this->schema WHERE TABLE_TYPE LIKE 'VIEW'";
        $rows = DB::select($sql);
        // dd($rows);

        foreach ($rows as $row) {
            $columnName = "Tables_in_{$this->schema}";
            $dbViews[$row->$columnName] = $this->getView($row->$columnName);
        }

        return $dbViews;
    }
}
