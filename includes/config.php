<?php
declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_PORT = '3306';
const DB_NAME = 'ai_solution_db';
const DB_USER = 'root';
const DB_PASS = '';

const ADMIN_SESSION_KEY = 'ai_solution_admin_id';

function app_url(string $path = ''): string
{
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/\\');
    if (str_starts_with($path, '/')) {
        return $path;
    }

    return ($base === '' ? '' : $base) . '/' . ltrim($path, '/');
}

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
