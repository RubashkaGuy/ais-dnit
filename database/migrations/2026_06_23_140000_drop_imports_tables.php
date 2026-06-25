<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('failed_import_rows');
        Schema::dropIfExists('imports');
    }

    public function down(): void
    {
        // Возврата нет: импорт удалён из приложения.
    }
};
