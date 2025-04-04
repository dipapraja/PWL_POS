<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'supplier_id'      => 1,
                'supplier_kode'    => 'S001',
                'supplier_nama'    => 'PT. ABC',
                'supplier_alamat'  => 'Jl. Raya No. 1',
            ],
            [
                'supplier_id'      => 2,
                'supplier_kode'    => 'S002',
                'supplier_nama'    => 'PT. DEF',
                'supplier_alamat'  => 'Jl. Raya No. 2',
            ],
            [
                'supplier_id'      => 3,
                'supplier_kode'    => 'S003',
                'supplier_nama'    => 'PT. GHI',
                'supplier_alamat'  => 'Jl. Raya No. 3',
            ],
            [
                'supplier_id'      => 4,
                'supplier_kode'    => 'S004',
                'supplier_nama'    => 'PT. JKL',
                'supplier_alamat'  => 'Jl. Raya No. 4',
            ],
            [
                'supplier_id'      => 5,
                'supplier_kode'    => 'S005',
                'supplier_nama'    => 'PT. MNO',
                'supplier_alamat'  => 'Jl. Raya No. 5',
            ],
            [
                'supplier_id'      => 6,
                'supplier_kode'    => 'S006',
                'supplier_nama'    => 'PT. PQR',
                'supplier_alamat'  => 'Jl. Raya No. 6',
            ],
            [
                'supplier_id'      => 7,
                'supplier_kode'    => 'S007',
                'supplier_nama'    => 'PT. STU',
                'supplier_alamat'  => 'Jl. Raya No. 7',
            ],
            [
                'supplier_id'      => 8,
                'supplier_kode'    => 'S008',
                'supplier_nama'    => 'PT. VWX',
                'supplier_alamat'  => 'Jl. Raya No. 8',
            ],
            [
                'supplier_id'      => 9,
                'supplier_kode'    => 'S009',
                'supplier_nama'    => 'PT. YZA',
                'supplier_alamat'  => 'Jl. Raya No. 9',
            ],
            [
                'supplier_id'      => 10,
                'supplier_kode'    => 'S010',
                'supplier_nama'    => 'PT. BCD',
                'supplier_alamat'  => 'Jl. Raya No. 10',
            ],
        ];

        DB::table('m_supplier')->insert($data);
    }
}