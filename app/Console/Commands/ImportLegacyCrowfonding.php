<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDO;
use Throwable;

class ImportLegacyCrowfonding extends Command
{
    protected $signature = 'crowfonding:import-legacy';
    protected $description = 'Importa usuarios, equipos y tickets de la base MySQL antigua a la base actual.';

    public function handle(): int
    {
        $legacy = config('database.connections.legacy_mysql');
        $host = $legacy['host'] ?? null;
        $database = $legacy['database'] ?? null;
        $username = $legacy['username'] ?? null;
        $password = $legacy['password'] ?? null;
        if (! $host || ! $database || ! $username || $password === null || $password === '') {
            $this->components->error('Configura LEGACY_DB_HOST, LEGACY_DB_DATABASE, LEGACY_DB_USERNAME y LEGACY_DB_PASSWORD en el .env local.');
            return self::FAILURE;
        }

        try {
            $source = new PDO(
                'mysql:host='.$host.';port='.$legacy['port'].';dbname='.$database.';charset=utf8mb4',
                $username,
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false],
            );
            foreach (['users', 'products', 'tickets'] as $table) {
                if (DB::table($table)->exists()) {
                    $this->components->error("La tabla destino {$table} ya tiene registros; se cancela para no duplicar ni sobrescribir.");
                    return self::FAILURE;
                }
            }
            $users = $source->query('SELECT * FROM usuarios ORDER BY id')->fetchAll();
            $products = $source->query('SELECT * FROM productos_venta ORDER BY id')->fetchAll();
            $tickets = $source->query('SELECT * FROM tickets ORDER BY id')->fetchAll();

            DB::transaction(function () use ($users, $products, $tickets) {
                foreach ($users as $row) {
                    DB::table('users')->insert([
                        'id' => $row['id'], 'name' => $row['nombre'], 'username' => $row['username'] ?: null,
                        'email' => $row['correo'], 'phone' => $row['telefono'] ?: null,
                        'password' => $row['password'], 'role' => $row['rol'] === 'admin' ? 'admin' : 'cliente',
                        'avatar' => $row['avatar'] ?: null, 'created_at' => $row['created_at'] ?? null,
                        'updated_at' => $row['updated_at'] ?? null,
                    ]);
                }
                foreach ($products as $row) {
                    DB::table('products')->insert([
                        'id' => $row['id'], 'name' => $row['nombre'], 'price' => (string) $row['precio'],
                        'image' => basename($row['imagen'] ?: 'laptop_hp.jpg'), 'description' => $row['descripcion'],
                        'created_at' => $row['created_at'] ?? null, 'updated_at' => $row['updated_at'] ?? null,
                    ]);
                }
                foreach ($tickets as $row) {
                    DB::table('tickets')->insert([
                        'id' => $row['id'], 'user_id' => $row['usuario_id'], 'service' => $row['servicio'],
                        'note' => $row['nota'], 'address' => $row['direccion'], 'status' => $row['estado_proceso'],
                        'created_at' => $row['created_at'] ?? null, 'updated_at' => $row['updated_at'] ?? null,
                    ]);
                }
            });
            $this->components->info(count($users).' usuarios, '.count($products).' equipos y '.count($tickets).' tickets importados.');
            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            $this->components->error('La importación falló y fue revertida. Revisa las tablas y el registro de Laravel; no se imprime ninguna clave.');
            return self::FAILURE;
        }
    }
}
