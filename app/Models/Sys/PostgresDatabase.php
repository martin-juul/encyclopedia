<?php

namespace App\Models\Sys;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * App\Models\Sys\PostgresDatabase
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\PostgresDatabase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\PostgresDatabase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Sys\PostgresDatabase query()
 * @mixin \Eloquent
 */
class PostgresDatabase extends Model
{
    public static function getAllTableSizes(): Collection
    {
        $query = \DB::raw("
        SELECT
            nspname || '.' || relname AS \"schema_table\",
            pg_size_pretty ( pg_total_relation_size ( C.oid ) ) AS \"total_size\"
        FROM
            pg_class
            C LEFT JOIN pg_namespace N ON ( N.oid = C.relnamespace )
        WHERE
            nspname NOT IN ( 'pg_catalog', 'information_schema' )
            AND C.relkind <> 'i'
            AND nspname !~ '^pg_toast'
        ORDER BY
            pg_total_relation_size ( C.oid ) DESC
            LIMIT 20;
        ");

        $tables = \DB::getPdo()->query($query)->fetchAll();

        $collection = collect([]);

        foreach ($tables as $table) {
            $collection->push(['table' => $table['schema_table'], 'size' => $table['total_size']]);
        }

        return $collection;
    }
}
