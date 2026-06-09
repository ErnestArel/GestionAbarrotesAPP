<?php
declare(strict_types=1);

final class FakeDatabase
{
    public array $consultarQueue = [];
    public array $consultas = [];
    public array $ejecuciones = [];

    public function queueConsultar(array $result): void
    {
        $this->consultarQueue[] = $result;
    }

    public function consultar(string $sql, array $params = []): array
    {
        $this->consultas[] = [
            'sql' => $this->normalizeSql($sql),
            'params' => $params,
        ];

        return array_shift($this->consultarQueue) ?? [];
    }

    public function ejecutar(string $sql, array $params = []): bool
    {
        $this->ejecuciones[] = [
            'sql' => $this->normalizeSql($sql),
            'params' => $params,
        ];

        return true;
    }

    private function normalizeSql(string $sql): string
    {
        return trim((string) preg_replace('/\s+/', ' ', $sql));
    }
}
