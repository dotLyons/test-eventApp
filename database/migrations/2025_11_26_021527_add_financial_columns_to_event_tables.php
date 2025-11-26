<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event_tables', function (Blueprint $table) {
            Schema::table('products', function (Blueprint $table) {
                $table->decimal('price', 10, 2)->default(0)->after('name');
            });

            Schema::table('orders', function (Blueprint $table) {
                $table->boolean('is_paid')->default(false)->after('status');
            });

            Schema::table('order_items', function (Blueprint $table) {
                $table->decimal('price', 10, 2)->default(0)->after('quantity');
            });

            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_table_id')->constrained('event_tables');
                $table->decimal('amount', 10, 2);
                $table->enum('method', ['cash', 'transfer']);
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->useCurrent();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_tables', function (Blueprint $table) {
            // Si hay que deshacer cambios
            Schema::dropIfExists('payments');

            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('price');
            });

            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('is_paid');
            });

            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('price');
            });
        });
    }
};
