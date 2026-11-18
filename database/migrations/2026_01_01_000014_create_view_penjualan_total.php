<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
            CREATE OR REPLACE VIEW v_penjualan_total AS
            SELECT p.id_penjualan,
                   COALESCE(SUM(d.qty_detail_penjualan), 0) AS total_item,
                   COALESCE(SUM(d.qty_detail_penjualan * d.harga_satuan_detail_penjualan), 0) AS total_penjualan,
                   GREATEST(p.bayar_penjualan - COALESCE(SUM(d.qty_detail_penjualan * d.harga_satuan_detail_penjualan), 0), 0) AS kembalian_penjualan
            FROM penjualan p
            LEFT JOIN detail_penjualan d ON d.id_penjualan = p.id_penjualan
            GROUP BY p.id_penjualan, p.bayar_penjualan
        SQL);
    }

    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_penjualan_total');
    }
};
