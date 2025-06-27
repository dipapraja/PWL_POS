<?php
  
namespace App\Http\Controllers;
  
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DataTables\KategoriDataTable;
use App\Models\KategoriModel;
use Illuminate\Http\RedirectResponse;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

  class KategoriController extends Controller
{
    public function index(){
        $breadcrumb = (object) [
            'title' => 'Daftar Kategori',
            'list' => ['Home', 'Kategori']
        ];

        $page = (object) [
            'title' => 'Daftar kategori yang terdaftar dalam sistem'
        ];

        $activeMenu = 'kategori';
        return view('kategori.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    // public function list(){
    //     $kategori = KategoriModel::all();
    //     return DataTables::of($kategori)
    //         ->addIndexColumn()
    //         ->addColumn('aksi', function ($kategori) {
    //                 // $btn = '<a href="' . url('/kategori/' . $kategori->kategori_id . '/edit') . '" class="btn btn-warning btn-sm">Edit</a> ';
    //                  // $btn .= '<form class="d-inline-block" method="POST" action="' . url('/kategori/' . $kategori->kategori_id) . '">'
    //                  //     . csrf_field() . method_field('DELETE') .
    //                  //     '<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';
    //                  // return $btn;
    //                  $btn = '<button onclick="modalAction(\''.url('/kategori/' . $kategori->kategori_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> '; 
    //                  $btn .= '<button onclick="modalAction(\''.url('/kategori/' . $kategori->kategori_id . '/delete_ajax').'\')"  class="btn btn-danger btn-sm">Hapus</button> ';
    //             return $btn;
    //         })
    //         ->rawColumns(['aksi'])
    //         ->make(true);
    // }

    public function create(){
        $breadcrumb = (object) [
            'title' => 'Tambah Kategori',
            'list' => ['Home', 'Kategori', 'Tambah']
        ];

        $page = (object) [
            'title' => 'Tambah kategori baru'
        ];

        $activeMenu = 'kategori';
        return view('kategori.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function store(Request $request){
        $request->validate([
            'kode' => 'required|string|min:3|max:10|unique:m_kategori,kategori_kode',
            'nama' => 'required|string|max:100',
        ]);

        KategoriModel::create([
            'kategori_kode' => $request->kode,
            'kategori_nama' => $request->nama,
        ]);

        return redirect('/kategori')->with('success', 'Data kategori berhasil disimpan');
    }

    public function edit(string $id){
        $kategori = KategoriModel::find($id);
        $breadcrumb = (object) [
            'title' => 'Edit Kategori',
            'list' => ['Home', 'Kategori', 'Edit']
        ];

        $page = (object) [
            'title' => 'Edit kategori'
        ];

        $activeMenu = 'kategori';
            return view('kategori.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'kategori' => $kategori, 'activeMenu' => $activeMenu]);
    }
     
    public function update(Request $request, string $id){
        $request->validate([
            'kode' => 'required|string|min:3|max:10|unique:m_kategori,kategori_kode',
            'nama' => 'required|string|max:100',
        ]);
        
        KategoriModel::find($id)->update([
            'kategori_kode' => $request->kode,
            'kategori_nama' => $request->nama,
        ]);

        return redirect('/kategori')->with('success', 'Data kategori berhasil diubah');
        $data = DB::table('m_kategori')->get();
        return view('kategori', ['data' => $data]);
        
    }
    
    public function destroy(string $id){
        $check = KategoriModel::find($id);
            if (!$check) {
                return redirect('/kategori')->with('error', 'Data kategori tidak ditemukan');
            }
     
            try {
                KategoriModel::destroy($id);
     
                return redirect('/kategori')->with('success', 'Data kategori berhasil dihapus');
            } catch (\Illuminate\Database\QueryException $e) {
                return redirect('/kategori')->with('error', 'Data kategori gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
            }
        }

        public function create_ajax()
        {
            $kategori = KategoriModel::select('kategori_kode', 'kategori_nama')->get();
    
            return view('kategori.create_ajax')
                ->with('kategori', $kategori);
        }
    
        public function store_ajax(Request $request)
        {
            //cek apakah request berupa ajax
            if ($request->ajax() || $request->wantsJson()) {
                $rules = [
                    'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode',
                    'kategori_nama' => 'required|string|max:100',
                ];
    
                // use Illiminate\Support\Facades\Validator
                $validator = Validator::make($request->all(), $rules);
    
                if ($validator->fails()) {
                    return response()->json([
                        'status' => false, //responAse status, false: error/gagal, true: berhasil
                        'message' => 'Validasi Gagal',
                        'msgField' => $validator->errors(), //pesan error validasi
                    ]);
                }
                KategoriModel::create(
                    [
                        'kategori_kode' => $request->kategori_kode,
                        'kategori_nama' => $request->kategori_nama,
                    ]
                );
                return response()->json([
                    'status' => true, //response status, false: error/gagal, true: berhasil
                    'message' => 'Data Kategori Berhasil Disimpan',
                ]);
            }
            redirect('/');
        }
    
        // Ambil data Kategori dalam bentuk json untuk datatables
        public function list(Request $request)
        {
            $kategori = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama');
    
            // filter data Kategori brdasarkan kategori_id
            if ($request->kategori_id) {
                $kategori->where('kategori_id', $request->kategori_id);
            }
    
            return DataTables::of($kategori)
                // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex)
                ->addIndexColumn()
                ->addColumn('aksi', function ($kategori) { // menambahkan kolom aksi
                    $btn = '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id .
                        '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                    $btn .= '<button onclick="modalAction(\'' . url('/kategori/' . $kategori->kategori_id .
                        '/delete_ajax') . '\')"  class="btn btn-danger btn-sm">Hapus</button> ';
                    return $btn;
                })
                ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html
                ->make(true);
        }
    
        public function edit_ajax(string $id)
        {
            $kategori = KategoriModel::find($id);
            // $kategori = KategoriModel::select('kategori_id', 'kategori_nama')->get();
    
            return view('kategori.edit_ajax', ['kategori' => $kategori]);
        }
    
        public function update_ajax(Request $request, $id)
        {
            // cek apakah request dari ajax
            if ($request->ajax() || $request->wantsJson()) {
                $rules = [
                    'kategori_kode' => 'required|string|min:3|unique:m_kategori,kategori_kode,'  . $id . ',kategori_id',
                    'kategori_nama' => 'required|string|max:100',
                ];
                // use Illuminate\Support\Facades\Validator; 
                $validator = Validator::make($request->all(), $rules);
    
                if ($validator->fails()) {
                    return response()->json([
                        'status'   => false,    // respon json, true: berhasil, false: gagal 
                        'message'  => 'Validasi gagal.',
                        'msgField' => $validator->errors()  // menunjukkan field mana yang error 
                    ]);
                }
    
                $check = KategoriModel::find($id);
                if ($check) {
                    $check->update($request->all());
                    return response()->json([
                        'status'  => true,
                        'message' => 'Data berhasil diupdate'
                    ]);
                } else {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Data tidak ditemukan'
                    ]);
                }
            }
            return redirect('/');
        }
    
        public function confirm_ajax(string $id)
        {
            $kategori = KategoriModel::find($id);
            return view('kategori.confirm_ajax', ['kategori' => $kategori]);
        }
    
        public function delete_ajax(Request $request, $id)
        {
            // cek apakah request dari ajax
            if ($request->ajax() || $request->wantsJson()) {
                $kategori = KategoriModel::find($id);
                if ($kategori) {
                    $kategori->delete();
                    return response()->json([
                        'status'  => true,
                        'message' => 'Data berhasil dihapus'
                    ]);
                } else {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Data tidak ditemukan'
                    ]);
                }
            }
            return redirect('/');
        }
        public function import()
    {
        return view('kategori.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                // validasi file harus xls atau xlsx, max 1MB
                'file_kategori' => ['required', 'mimes:xlsx', 'max:1024']
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors()
                ]);
            }
            $file = $request->file('file_kategori'); // ambil file dari request

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
                            'kategori_id' => $value['A'],
                            'kategori_kode' => $value['B'],
                            'kategori_nama' => $value['C'],
                            'created_at' => now(),
                        ];
                    }
                }

                if (count($insert) > 0) {
                    // insert data ke database, jika data sudah ada, maka diabaikan
                    KategoriModel::insertOrIgnore($insert);
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
        // ambil data kategori yang akan di export
        $kategori = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama')
                ->orderBy('kategori_id')
                ->get();
        
        // load library excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // ambil sheet yang aktif

        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode kategori');
        $sheet->setCellValue('C1', 'Nama kategori');

        $sheet->getStyle('A1:F1')->getFont()->setBold(true); // bold header

        $no = 1; // nomor data dimulai dari 1
        $baris = 2; // baris data dimulai dari baris ke 2
        foreach ($kategori as $key => $value) {
            $sheet->setCellValue('A'.$baris, $no);
            $sheet->setCellValue('B'.$baris, $value->kategori_kode);
            $sheet->setCellValue('C'.$baris, $value->kategori_nama);
            $baris++;
            $no++;
        }

        foreach(range('A', 'F') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true); // set auto size untuk kolom
        }

        $sheet->setTitle('Data kategori'); // set title sheet

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data kategori ' . date('Y-m-d H:i:s') . '.xlsx';

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
        $kategori = KategoriModel::select('kategori_id', 'kategori_kode', 'kategori_nama')
            ->orderBy('kategori_id')
            ->orderBy('kategori_kode')
            ->get();

        // 
        $pdf = Pdf::loadView('kategori.export_pdf', ['kategori' => $kategori]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption("isRemoteEnabled", true);
        $pdf->render();

        return $pdf->stream('Data kategori '.date('Y-m-d H:i:s').'.pdf');
    }
}