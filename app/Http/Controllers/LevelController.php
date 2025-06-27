<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Models\LevelModel;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;

class LevelController extends Controller
{
    
    // Menampilkan halaman daftar level.
    public function index(): View {
        $breadcrumb = (object)[
            'title' => 'Daftar Level',
            'list'  => ['Home', 'Level']
        ];

        $page = (object)[
            'title' => 'Daftar level yang terdaftar dalam sistem'
        ];

        $activeMenu = 'level';
        // Ambil semua data level, bisa digunakan di view jika diperlukan.
        $level = LevelModel::all();
        
        return view('level.index', compact('breadcrumb', 'page', 'level', 'activeMenu'));
    }

    //  Mengambil data level untuk DataTables.
    public function list(Request $request)
    {
        // Query tanpa ->get() agar mendukung server-side processing DataTables
        $levels = LevelModel::select('level_id', 'level_kode', 'level_nama');

        return DataTables::of($levels)
            ->addIndexColumn()
            ->addColumn('aksi', function($level) {
                
                 $btn  = '<button onclick="modalAction(\''.url('/level/' . $level->level_id . '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button> '; 
                 $btn .= '<button onclick="modalAction(\''.url('/level/' . $level->level_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> '; 
                 $btn .= '<button onclick="modalAction(\''.url('/level/' . $level->level_id . '/delete_ajax').'\')"  class="btn btn-danger btn-sm">Hapus</button> '; 
                
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    } 

    //  Menampilkan detail level.
    public function show(string $id): View {
        $level = LevelModel::findOrFail($id);

        $breadcrumb = (object)[
            'title' => 'Detail Level',
            'list'  => ['Home', 'Level', 'Detail']
        ];

        $page = (object)[
            'title' => 'Detail Level'
        ];

        $activeMenu = 'level';

        return view('level.show', compact('breadcrumb', 'level', 'page', 'activeMenu'));
    }

    // Menampilkan form untuk menambahkan level baru.
    public function create(): View {
        $breadcrumb = (object)[
            'title' => 'Tambah Level',
            'list'  => ['Home', 'Level']
        ];

        $page = (object)[
            'title' => 'Tambah level baru'
        ];

        $activeMenu = 'level';
        // Jika diperlukan, ambil data level lainnya.
        $level = LevelModel::all();

        return view('level.create', compact('breadcrumb', 'page', 'activeMenu', 'level'));
    }

    //   Menyimpan data level baru.
    public function store(Request $request): RedirectResponse {
        $validatedData = $request->validate([
            'level_id'   => 'required|int',
            'level_nama' => 'required|string',
            'level_kode' => 'required|string',
        ], [
            'level_id.required'   => 'ID Level harus diisi.',
            'level_nama.required' => 'Nama Level harus diisi.',
            'level_kode.required' => 'Kode Level harus diisi.',
        ]);

        // Jika Anda menggunakan Mass Assignment, pastikan LevelModel memiliki properti $fillable
        LevelModel::create($validatedData);

        return redirect('/level')->with('success', 'Data Level berhasil ditambahkan');
    }

    // Menampilkan form edit level.
    public function edit($id): View {
        $level = LevelModel::findOrFail($id);

        $breadcrumb = (object)[
            'title' => 'Edit Level',
            'list'  => ['Home', 'Level', 'Edit']
        ];

        $page = (object)[
            'title' => 'Edit Level'
        ];

        $activeMenu = 'level';

        return view('level.edit', compact('breadcrumb', 'page', 'level', 'activeMenu'));
    }

    //  Memperbarui data level.
    public function update(Request $request, $id): RedirectResponse {
        $validated = $request->validate([
            'level_id'   => 'required',
            'level_nama' => 'required|string',
            'level_kode' => 'required',
        ]);

        $level = LevelModel::findOrFail($id);
        $level->update($validated);

        return redirect('/level')->with('success', 'Data Level berhasil diperbarui');
    }

    //  Menghapus data level.
    public function destroy($id): RedirectResponse {
        $level = LevelModel::findOrFail($id);
        $level->delete();

        return redirect('/level')->with('success', 'Data Level berhasil dihapus');
    }

    public function create_ajax()
    {
        return view('level.create_ajax'); // Mengarahkan ke view untuk create level ajax
    }

    // Menyimpan data level menggunakan AJAX
    public function store_ajax(Request $request)
    {
        // Cek apakah request berasal dari AJAX atau request JSON
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_kode' => 'required|string|min:3|unique:m_level,level_kode',
                'level_nama' => 'required|string|max:255',
            ];

            // Validasi data request
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                // Jika validasi gagal, kembalikan response JSON dengan pesan error
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            // Jika validasi sukses, simpan data level ke dalam database
            LevelModel::create([
                'level_kode' => $request->level_kode,
                'level_nama' => $request->level_nama
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Data level berhasil disimpan',
            ]);
        }

        return redirect('/'); // Jika bukan request AJAX, arahkan kembali ke halaman utama
    }

    public function edit_ajax($id)
    {
        $level = levelModel::find($id);

        if (!$level) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan.'
            ]);
        }

        return view('level.edit_ajax', compact('level'));
    }


    // Memperbarui data level menggunakan AJAX
    public function update_ajax(Request $request, $id)
    {
        // Cek apakah request berasal dari AJAX
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_kode' => 'required|string|min:3|unique:m_level,level_kode,' . $id . ',level_id',
                'level_nama' => 'required|string|max:255',
            ];

            // Validasi data request
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal.',
                    'msgField' => $validator->errors(),
                ]);
            }

            $level = levelModel::find($id);
            if ($level) {
                // Jika level ditemukan, update datanya
                $level->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Data level berhasil diupdate'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data level tidak ditemukan'
                ]);
            }
        }

        return redirect('/'); // Jika bukan request AJAX, arahkan kembali ke halaman utama
    }

    // Konfirmasi penghapusan data level menggunakan AJAX
    public function confirm_ajax(string $id)
    {
        $level = levelModel::find($id);

        // Pastikan level ditemukan
        if (!$level) {
            return response()->json([
                'status' => false,
                'message' => 'Level tidak ditemukan.'
            ]);
        }

        // Kirimkan data level ke view konfirmasi
        return view('level.confirm_ajax', ['level' => $level]);
    }

    // Menghapus data level menggunakan AJAX
    public function delete_ajax(Request $request, string $id)
    {
        // Cek apakah request berasal dari AJAX
        if ($request->ajax() || $request->wantsJson()) {
            $level = levelModel::find($id);

            if ($level) {
                // Jika level ditemukan, hapus dari database
                $level->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data level berhasil dihapus',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data level tidak ditemukan',
                ]);
            }
        }

        return redirect('/');
    }

    public function show_ajax(string $id)
    {
        $level = LevelModel::find($id);
    
        if (!$level) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    
        $breadcrumb = (object)[
            'title' => 'Detail Level',
            'list'  => ['Home', 'Level', 'Detail']
        ];
    
        $page = (object)[
            'title' => 'Detail Level'
        ];
    
        $activeMenu = 'level';
    
        return response()->json([
            'status' => true,
            'breadcrumb' => $breadcrumb,
            'level' => $level,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }
    
    public function import()
    {
        return view('level.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                // validasi file harus xls atau xlsx, max 1MB
                'file_level' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
            $file = $request->file('file_level'); // ambil file dari request

            $reader = IOFactory::createReader('Xlsx'); // load reader file excel
            $reader->setReadDataOnly(true); // hanya membaca data
            $spreadsheet = $reader->load($file->getRealPath()); // load file excel
            $sheet = $spreadsheet->getActiveSheet();      // ambil sheet yang aktif

            $data = $sheet->toArray(null, false, true, true);    // ambil data excel

            $insert = [];
            if (count($data) > 1) { // jika data lebih dari 1 baris
                foreach ($data as $baris => $value) {
                    if ($baris > 1) { // baris ke 1 adalah header, maka lewati
                        $insert[] = [
                            'level_id' => $value['A'],
                            'level_kode' => $value['B'],
                            'level_nama' => $value['C'],
                            'created_at' => now(),
                        ];
                    }
                }

                if (count($insert) > 0) {
                    // insert data ke database, jika data sudah ada, maka diabaikan
                    LevelModel::insertOrIgnore($insert);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'Data berhasil diimport'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Tidak ada data yang diimport'
                ]);
            }
        }
        return redirect('/');
    }

    public function export_excel()
    {
        // ambil data level yang akan di export
        $level = LevelModel::select('level_id', 'level_kode', 'level_nama')
                ->orderBy('level_id')
                ->get();
        
        // load library excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode level');
        $sheet->setCellValue('C1', 'Nama level');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true); // bold header

        $no = 1; // nomor data dimulai dari 1
        $baris = 2; // baris data dimulai dari baris ke 2
        foreach ($level as $key => $value) {
            $sheet->setCellValue('A'.$baris, $no);
            $sheet->setCellValue('B'.$baris, $value->level_kode);
            $sheet->setCellValue('C'.$baris, $value->level_nama);
            $baris++;
            $no++;
        }

        foreach(range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true); // set auto size untuk kolom
        }

        $sheet->setTitle('Data level'); // set title sheet

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data level ' . date('Y-m-d H:i:s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer->save('php://output');
        exit;
    } // end function export_excel

    public function export_pdf() {
        $level = LevelModel::select('level_id', 'level_kode', 'level_nama')
            ->orderBy('level_id')
            ->orderBy('level_kode')
            ->get();

        // 
        $pdf = Pdf::loadView('level.export_pdf', ['level' => $level]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->stream('Data level '.date('Y-m-d H:i:s').'.pdf');
    }
}