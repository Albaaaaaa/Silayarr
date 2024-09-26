<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <div class="text-center mt-4 no-print">
            <button class="btn btn-custom me-2 mb-2" onclick="window.print()"><i class='bx bx-printer me-1'></i> Cetak</button>
            <button class="btn btn-custom me-2 mb-2" onclick="exportToExcel()"><i class='bx bx-spreadsheet me-1'></i> Ekspor ke Excel</button>
            <button class="btn btn-custom mb-2" onclick="exportToPDF()"><i class='bx bx-file me-1'></i> Ekspor ke PDF</button>
        </div>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); }
        .card { border: none; border-radius: 20px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); overflow: hidden; backdrop-filter: blur(10px); background-color: rgba(255, 255, 255, 0.8); }
        .card-header { background: linear-gradient(135deg, #6e8efb, #a777e3); color: white; border-bottom: none; padding: 20px; }
        .table thead th { background-color: rgba(110, 142, 251, 0.1); color: #4a4a4a; border: none; font-weight: 600; }
        .table-hover tbody tr:hover { background-color: rgba(167, 119, 227, 0.1); transition: all 0.3s; }
        .btn-custom { background: linear-gradient(135deg, #6e8efb, #a777e3); color: white; border: none; transition: all 0.3s; border-radius: 50px; padding: 12px 25px; font-weight: 600; box-shadow: 0 5px 15px rgba(110, 142, 251, 0.4); }
        .btn-custom:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(110, 142, 251, 0.6); }
        @media print {
            body { background: none; font-size: 7pt; margin: 0; padding: 0; }
            .container-fluid { width: 100%; padding: 0; margin: 0; }
            .card { box-shadow: none; border: none; }
            .card-header { background: none; color: #000; border-bottom: 1pt solid #000; padding: 5pt 0; }
            .table { width: 100% !important; border-collapse: collapse; font-size: 7pt; }
            .table thead th { background-color: #f8f9fa; border: 0.5pt solid #000; font-weight: bold; padding: 2pt; }
            .table td, .table th { padding: 2pt; border: 0.5pt solid #000; }
            .table-hover tbody tr:hover { background-color: transparent; }
            .no-print, .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_paginate { display: none !important; }
            #dokumenTable {
                page-break-inside: auto;
            }
            #dokumenTable tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            #dokumenTable td {
                background-color: transparent !important;
                border: 0.5pt solid #000 !important;
            }
        }
        @page {
            size: landscape;
            margin: 1cm;
        }
        #dokumenTable { width: 100% !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: linear-gradient(135deg, #6e8efb, #a777e3) !important; border-color: #6e8efb !important; color: white !important; }
        .animate__animated { animation-duration: 0.8s; }
        .table-responsive { border-radius: 15px; overflow: hidden; }
        .dataTables_wrapper { padding: 20px; background-color: rgba(255, 255, 255, 0.9); border-radius: 0 0 20px 20px; }
        #dokumenTable { border-collapse: separate; border-spacing: 0 10px; }
        #dokumenTable tbody tr { box-shadow: 0 5px 10px rgba(0, 0, 0, 0.05); border-radius: 10px; }
        #dokumenTable td { background-color: white; border: none; padding: 15px; }
        #dokumenTable td:first-child { border-top-left-radius: 10px; border-bottom-left-radius: 10px; }
        #dokumenTable td:last-child { border-top-right-radius: 10px; border-bottom-right-radius: 10px; }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-3">
        <div class="card">
            <div class="card-header">
                <h2 class="text-center mb-0 fw-bold"><?= $title ?></h2>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="dokumenTable" class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>No Berkas</th>
                                <th>Kode Klasifikasi</th>
                                <th>Uraian Berkas</th>
                                <th>No Item</th>
                                <th>Uraian Isi Berkas</th>
                                <th>OPD</th>
                                <th>Tanggal Berkas</th>
                                <th>Tingkat Perkembangan</th>
                                <th>Jumlah Berkas</th>
                                <th>Kondisi Berkas</th>
                                <th>Tahun Inaktif</th>
                                <th>Nasib Akhir</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dokumen as $index => $doc): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= $doc['no_berkas'] ?></td>
                                <td><?= $doc['kode_klasifikasi'] ?></td>
                                <td><?= $doc['judul_dokumen'] ?></td>
                                <td><?= $doc['no_item'] ?></td>
                                <td><?= $doc['deskripsi_dokumen'] ?></td>
                                <td><?= $doc['opd'] ?></td>
                                <td><?= date('d/m/Y', strtotime($doc['tgl_upload'])) ?></td>
                                <td><?= $doc['tingkat_perkembangan'] ?></td>
                                <td><?= $doc['jumlah_berkas'] ?></td>
                                <td><?= $doc['kondisi_berkas'] ?></td>
                                <td><?= $doc['inaktif'] ?></td>
                                <td><?= $doc['nasib_akhir'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.17.0/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.20/jspdf.plugin.autotable.min.js"></script>
    <script>
        $(document).ready(function() {
            var table = $('#dokumenTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
                },
                responsive: true,
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                initComplete: function() {
                    $('.dataTables_wrapper').addClass('animate__animated animate__fadeIn');
                },
                scrollX: true,
                autoWidth: false,
                columnDefs: [
                    { width: '3%', targets: 0 },
                    { width: '7%', targets: [1, 2, 4, 6, 7, 8, 9, 10, 11, 12] },
                    { width: '10%', targets: [3, 5] }
                ],
                paging: false,
                info: false,
                searching: false,
                ordering: false
            });

            // Menghapus kontrol DataTables saat mencetak
            window.onbeforeprint = function() {
                table.destroy();
            };

            // Menginisialisasi ulang DataTables setelah mencetak
            window.onafterprint = function() {
                table = $('#dokumenTable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json'
                    },
                    responsive: true,
                    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                         '<"row"<"col-sm-12"tr>>' +
                         '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                    initComplete: function() {
                        $('.dataTables_wrapper').addClass('animate__animated animate__fadeIn');
                    },
                    scrollX: true,
                    autoWidth: false,
                    columnDefs: [
                        { width: '3%', targets: 0 },
                        { width: '7%', targets: [1, 2, 4, 6, 7, 8, 9, 10, 11, 12] },
                        { width: '10%', targets: [3, 5] }
                    ],
                    paging: false,
                    info: false,
                    searching: false,
                    ordering: false
                });
            };
        });

        function exportToExcel() {
            // Membuat workbook baru
            const wb = XLSX.utils.book_new();
            
            // Mengambil data dari tabel
            const table = document.getElementById('dokumenTable');
            const ws = XLSX.utils.table_to_sheet(table);
            
            // Menyesuaikan lebar kolom
            const colWidths = [
                {wch: 5},  // No
                {wch: 15}, // No Berkas
                {wch: 15}, // Kode Klasifikasi
                {wch: 30}, // Uraian Berkas
                {wch: 10}, // No Item
                {wch: 30}, // Uraian Isi Berkas
                {wch: 20}, // OPD
                {wch: 15}, // Tanggal Berkas
                {wch: 20}, // Tingkat Perkembangan
                {wch: 15}, // Jumlah Berkas
                {wch: 15}, // Kondisi Berkas
                {wch: 15}, // Tahun Inaktif
                {wch: 15}  // Nasib Akhir
            ];
            ws['!cols'] = colWidths;
            
            // Menambahkan worksheet ke workbook
            XLSX.utils.book_append_sheet(wb, ws, "Dokumen");
            
            // Mengatur style untuk seluruh tabel
            const range = XLSX.utils.decode_range(ws['!ref']);
            for (let row = range.s.r; row <= range.e.r; row++) {
                for (let col = range.s.c; col <= range.e.c; col++) {
                    const cellRef = XLSX.utils.encode_cell({r: row, c: col});
                    if (!ws[cellRef]) continue;
                    ws[cellRef].s = {
                        font: { name: "Arial", sz: 11 },
                        alignment: { vertical: "center", wrapText: true },
                        border: {
                            top: { style: "thin" },
                            bottom: { style: "thin" },
                            left: { style: "thin" },
                            right: { style: "thin" }
                        }
                    };
                    
                    // Mengatur style khusus untuk header
                    if (row === range.s.r) {
                        ws[cellRef].s.font.bold = true;
                        ws[cellRef].s.fill = { fgColor: { rgb: "EEEEEE" } };
                        ws[cellRef].s.alignment.horizontal = "center";
                    }
                }
            }
            
            // Menyimpan file
            const fileName = '<?= $title ?>.xlsx';
            XLSX.writeFile(wb, fileName);
        }

        function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'pt', 'a4');
            doc.autoTable({
                html: '#dokumenTable',
                startY: 60,
                styles: { 
                    fontSize: 6,
                    cellPadding: 1,
                    lineColor: [0, 0, 0],
                    lineWidth: 0.1
                },
                headStyles: { 
                    fillColor: [240, 240, 240],
                    textColor: [0, 0, 0],
                    fontStyle: 'bold'
                },
                bodyStyles: {
                    textColor: [0, 0, 0]
                },
                alternateRowStyles: {
                    fillColor: [245, 245, 245]
                },
                didDrawPage: function(data) {
                    doc.setFontSize(12);
                    doc.setTextColor(40);
                    doc.text('<?= $title ?>', data.settings.margin.left, 40);
                }
            });
            doc.save('<?= $title ?>.pdf');
        }
    </script>
</body>
</html>