@extends('layouts.template')

@section('content')
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ $page->title }}</h3>
            <div class="card-tools">
                <a class="btn btn-primary" href="{{ url('kategori/create') }}">Tambah</a>
                <button onclick="modalAction('{{ url('/kategori/create_ajax') }}')"
                    class="btn btn-success">Tambah Ajax</button>
                <button onclick="modalAction('{{ url('/kategori/import') }}')" class="btn btn-info">Import kategori</button>
                <a href="{{ url('/kategori/export_excel') }}" class="btn btn-primary"><i class="fa fa-file-excel"></i>Export
                    kategori</a>
                <a href="{{ url('/kategori/export_pdf') }}" class="btn btn-warning"><i class="fa fa-file- pdf"></i> Export
                    kategori</a>
                <button onclick="modalAction('{{ url('/kategori/create_ajax') }}')" class="btn btn-success">Tambah Data
                    (Ajax)</button>
            </div>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <table class="table-bordered table-striped table-hover table-sm table" id="table_kategori">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="myModal" class="modal fade animate shake" tabindex="-1" role="dialog" data backdrop="static"
        data-keyboard="false" data-width="75%" aria-hidden="true"></div>
@endsection

@push('css')
@endpush

@push('js')
    <script>
        function modalAction(url = '') {
            $('#myModal').load(url, function () {
                $('#myModal').modal('show');
            });
        }
        $(document).ready(function () {
            var dataUser = $('#table_kategori').DataTable({
                serverSide: true,
                ajax: {
                    "url": "{{ url('kategori/list') }}",
                    "dataType": "json",
                    "type": "POST",
                },
                columns: [{
                    data: "DT_RowIndex",
                    className: "text-center",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "kategori_kode",
                    className: "",
                    orderable: true,
                    searchable: true
                },
                {
                    data: "kategori_nama",
                    className: "",
                    orderable: true,
                },
                {
                    data: "aksi",
                    className: "",
                    orderable: false,
                    searchable: false
                },
                ]
            });
        });
    </script>
@endpush