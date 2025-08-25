<?php

declare(strict_types=1);

use Zotel\Wallet\Models\WalletTransaction;
use Zotel\Wallet\Models\WalletTransfer;
use Zotel\Wallet\Models\Wallet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table((new Wallet())->getTable(), static function (Blueprint $table) {
            $table->softDeletesTz();
        });
        Schema::table((new WalletTransfer())->getTable(), static function (Blueprint $table) {
            $table->softDeletesTz();
        });
        Schema::table((new WalletTransaction())->getTable(), static function (Blueprint $table) {
            $table->softDeletesTz();
        });
    }

    public function down(): void
    {
        Schema::table((new Wallet())->getTable(), static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table((new WalletTransfer())->getTable(), static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table((new WalletTransaction())->getTable(), static function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
