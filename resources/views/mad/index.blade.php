@extends('layouts.app')
@section('content')
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
              <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                  <h3 class="font-weight-bold">MAD</h3>
                </div>
              </div>
            <div class="card shadow mb-4">
                <div class="py-3">
                    <h5 class="description ml-3">Unggah file untuk menambahkan data MAD baru, atau tambahkan ke kumpulan data MAD yang sudah ada. <br> Anda dapat mengunggah file Excel</h5>
                    <div class="button-group ml-3 mb-3">
                        <a href="{{ route('mad.create') }}">
                            <button type="button" class="btn btn-primary">Tambah MAD</button>
                        </a>
                        
                        <a href="{{ route('downloadmad') }}">
                            <button type="button" class="btn btn-warning" style="color:white;" download>Unduh Template</button>
                        </a>

                        <form action="{{ route('exportmad') }}" method="GET" style="display: inline;">
                            <input type="hidden" name="month" id="exportMonth">
                            <input type="hidden" name="year" id="exportYear">
                            <button type="submit" class="btn btn-success">Ekspor Data</button>
                        </form>
                    </div>
                
                    <div class="importdata ml-3">
                        <form action="{{ route('importmad') }}"  method="post" enctype="multipart/form-data" style="display: flex; align-items: center;">
                        @csrf
                        <input type="file" name="file" accept=".xlsx, .xls" style="margin-right: 10px;" required>
                            <button class="btn btn-info" type="submit">Unggah File</button>
                        </form>
                    </div>
                </div>
                <div class="card-body">

                <!-- Filter Bulan dan Tahun -->
                <div class="row mb-3">
    <div class="col-md-3">
        <label for="filterMonth">Bulan</label>
        <select id="filterMonth" class="form-control" style="color:black;">
            @php
                $months = [
                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                ];
                $currentMonth = date('n');
            @endphp
            <option value="" disabled selected>Pilih Bulan</option>
            @foreach ($months as $key => $month)
                <option value="{{ $key }}" >{{ $month }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label for="filterYear">Tahun</label>
        <select id="filterYear" class="form-control" style="color:black;">
            @php
                $currentYear = date('Y');
                $startYear = $currentYear - 5;
            @endphp
            <option value="" disabled selected>Pilih Tahun</option>
            @for ($year = $startYear; $year <= $currentYear; $year++)
                <option value="{{ $year }}">{{ $year }}</option>
            @endfor
        </select>
    </div>
    <div class="col-md-3 mt-2">
        <button class="btn btn-primary mt-4" onclick="applyMonthYearFilter()">Pilih</button>
    </div>
</div>


                <div class="dataTables_length mb-3" id="myDataTable_length">
                    <label for="entries"> Show
                    <select id="entries" name="myDataTable_length" aria-controls="myDataTable" onchange="changeEntries()" class>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries
                    </label>
                </div>

                <div id="myDataTable_filter" class="dataTables_filter">
                    <label for="search">search
                        <input id="search" placeholder>
                    </label>
                </div>                            
                    
                    <div class="table-responsive">
                    @include('components.alert')
                    <table class="table table-bordered">
                          <thead>      
                              <tr>
                                <th>No</th>
                                <th>No PBB/Amandemen</th>
                                <th>NIK (sesuai KTP)</th>
                                <th>Nama (sesuai KTP)</th>
                                <th>Unit Kerja Penempatan</th>
                                <th>Posisi</th>
                                <th>Kode Cabang Pembayaran</th>
                                <th>RCC Pembayaran</th>
                                <th>Tanggal Lembur</th>
                                <th>Cek Tanggal</th>
                                <th>L/K</th>
                                <th>Upah Pokok</th>
                                <th>Tunjangan Supervisor</th>
                                <th>Jam Mulai</th>
                                <th>Jam Selesai</th>
                                <th>Jumlah Jam Lembur per Hari</th>
                                <th>Jam Pertama</th>
                                <th>Jam Kedua</th>
                                <th>Jam Ketiga</th>
                                <th>Jam Keempat</th>
                                <th>Biaya Jam Pertama</th>
                                <th>Biaya Jam Kedua</th>
                                <th>Biaya Jam Ketiga</th>
                                <th>Biaya Jam Keempat</th>
                                <th>Subtotal</th>
                                <th>Management Fee (%)</th>
                                <th>Management Fee (besaran)</th>
                                <th>Total Sebelum PPN</th>
                                <th>Keterangan Lembur</th>
                                <th>Keterangan Perbaikan</th>
                                <th>Kode SLID</th>
                                <th>Action</th>
                              </tr>
                          </thead>
                          <tbody>
                          @php
                              $counter = 1; // Inisialisasi nomor urutan
                          @endphp

                          @foreach ($mad as $item)
                              <tr>
                                  <td>{{ $counter++ }}</td>
                                  <td>{{ $item->karyawan->no_amandemen }}</td>
                                  <td>{{ $item->karyawan->nik_ktp }}</td>
                                  <td>{{ $item->karyawan->nama_karyawan }}</td>
                                  <td>{{ $item->karyawan->penempatan->nama_unit_kerja }}</td>
                                  <td>{{ $item->karyawan->posisi->posisi }}</td>
                                  <td>{{ $item->karyawan->penempatan->kode_cabang_pembayaran }}</td>
                                  <td>{{ $item->karyawan->penempatan->rcc_pembayaran }}</td>
                                  <td>{{ $item->tanggal_lembur }}</td>
                                  <td>{{ $item->jenis_hari }}</td>
                                  <td>
                                      @if ($item->jenis_hari == "Kerja")
                                          K
                                      @elseif($item->jenis_hari == "Libur")
                                          L
                                      @endif
                                  </td>
                                  <td>{{ 'Rp ' . number_format($item->gaji, 0, ',', '.') }}</td>
                                  <td>{{ 'Rp ' . number_format($item->tunjangan, 0, ',', '.') }}</td>
                                  <td>{{ $item->jam_mulai }}</td>
                                  <td>{{ $item->jam_selesai }}</td>
                                  <td>{{ $item->jumlah_jam_lembur }}</td>
                                  <td>{{ $item->jam_pertama }}</td>
                                  <td>{{ $item->jam_kedua }}</td>
                                  <td>{{ $item->jam_ketiga }}</td>
                                  <td>{{ $item->jam_keempat }}</td>
                                  <td>{{ 'Rp ' . number_format($item->biaya_jam_pertama, 0, ',', '.') }}</td>
                                  <td>{{ 'Rp ' . number_format($item->biaya_jam_kedua, 0, ',', '.') }}</td>
                                  <td>{{ 'Rp ' . number_format($item->biaya_jam_ketiga, 0, ',', '.') }}</td>
                                  <td>{{ 'Rp ' . number_format($item->biaya_jam_keempat, 0, ',', '.') }}</td>
                                  <td>{{ 'Rp ' . number_format($item->subtotal, 0, ',', '.') }}</td>
                                  <td>{{ $item->karyawan->management_fee * 100 }}</td>
                                  <td>{{ 'Rp ' . number_format($item->management_fee_amount, 0, ',', '.') }}</td>
                                  <td>{{ 'Rp ' . number_format($item->total_sebelum_ppn, 0, ',', '.') }}</td>
                                  <td>{{ $item->keterangan_lembur }}</td>
                                  <td></td>
                                  <td>{{ $item->karyawan->penempatan->kode_slid }}</td>
                                  <td>
                                      <a href="{{ route('showmad', $item->id) }}">
                                          <button type="button" class="btn btn-rounded btn-icon" data-toggle="tooltip" title="Ubah">
                                              <i class="ti-pencil text-warning" style="font-weight: bold;"></i>
                                          </button>
                                      </a> 
                                      <form id="deleteForm-{{ $item->id }}" method="POST" action="{{ route('deletemad', $item->id) }}">
                                          @csrf
                                          <input name="_method" type="hidden" value="DELETE">
                                          <button type="button" class="btn btn-rounded btn-icon delete-btn" data-id="{{ $item->id }}" data-toggle="modal" data-target="#confirmModal" title="Hapus">
                                              <i class="ti-trash text-danger" style="font-weight: bold;"></i>
                                          </button>
                                      </form>
                                  </td>
                              </tr>
                          @endforeach   
                          </tbody>
                      </table>

                      <div class="dataTables_info" id="dataTableInfo" role="status" aria-live="polite">
    Showing <span id="showingStart">1</span> to <span id="showingEnd">10</span> of <span id="totalEntries">0</span> entries
</div>
        
<div class="dataTables_paginate paging_simple_numbers" id="myDataTable_paginate">
    
    <a href="#" class="paginate_button" id="doublePrevButton" onclick="doublePreviousPage()"><i class="ti-angle-double-left" aria-hidden="true"></i></a>
    <a href="#" class="paginate_button" id="prevButton" onclick="previousPage()"><i class="ti-angle-left" aria-hidden="true"></i></a>
    <span>
        <a id="pageNumbers" aria-controls="myDataTable" role="link" aria-current="page" data-dt-idx="0" tabindex="0"></a>
    </span>
    <a href="#" class="paginate_button" id="nextButton" onclick="nextPage()"><i class="ti-angle-right" aria-hidden="true"></i></a>
    <a href="#" class="paginate_button" id="doubleNextButton" onclick="doubleNextPage()"><i class="ti-angle-double-right" aria-hidden="true"></i></a>
</div>                     
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>

      <style>
         th, td {
            vertical-align: middle;
            text-align: center;
        }

        .dataTables_paginate {
            text-align: center;
        }

        .paginate_button {
            display: inline-block;
            margin: 5px;
            text-align: center;
            border: 1px solid #000; 
            padding: 5px 10px;
        }

        @media (max-width: 768px) {
            .paginate_button {
                padding: 3px 6px;
            }
        }

        @media (max-width: 576px) {
            .dataTables_paginate {
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
            }
            .paginate_button {
                padding: 2px 4px;
                margin: 2px;
            }
        }

        .btn-active {
            background-color: #007bff;
            color: #fff;
        }
    </style>

    <script>
        var itemsPerPage = 10; // Ubah nilai ini sesuai dengan jumlah item per halaman
        var currentPage = 1;
        var filteredData = [];
        
        function initializeData() {
            var tableRows = document.querySelectorAll("table tbody tr");
            filteredData = Array.from(tableRows); // Konversi NodeList ke array
            updatePagination();
        }

        initializeData();

        function doublePreviousPage() {
            if (currentPage > 1) {
                currentPage = 1;
                updatePagination();
            }
        }
        
        function nextPage() {
            var totalPages = Math.ceil(document.querySelectorAll("table tbody tr").length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage++;
                updatePagination();
            }
        }

        function doubleNextPage() {
            var totalPages = Math.ceil(document.querySelectorAll("table tbody tr").length / itemsPerPage);
            if (currentPage < totalPages) {
                currentPage = totalPages;
                updatePagination();
            }
        }

        function previousPage() {
            if (currentPage > 1) {
                currentPage--;
                updatePagination();
            }
        }

        function updatePagination() {
            var startIndex = (currentPage - 1) * itemsPerPage;
            var endIndex = startIndex + itemsPerPage;

            var tableRows = document.querySelectorAll("table tbody tr");
            tableRows.forEach(function (row) {
                row.style.display = 'none';
            });

            for (var i = startIndex; i < endIndex && i < filteredData.length; i++) {
                filteredData[i].style.display = 'table-row';
            }

            var totalPages = Math.ceil(filteredData.length / itemsPerPage);
            var pageNumbers = document.getElementById('pageNumbers');
            pageNumbers.innerHTML = '';

            var totalEntries = filteredData.length;

            document.getElementById('showingStart').textContent = startIndex + 1;
            document.getElementById('showingEnd').textContent = Math.min(endIndex, totalEntries);
            document.getElementById('totalEntries').textContent = totalEntries;

            var pageRange = 3;
            var startPage = Math.max(1, currentPage - Math.floor(pageRange / 2));
            var endPage = Math.min(totalPages, startPage + pageRange - 1);

            for (var i = startPage; i <= endPage; i++) {
                var pageButton = document.createElement('button');
                pageButton.className = 'btn btn-primary btn-sm mr-1 ml-1';
                pageButton.textContent = i;
                if (i === currentPage) {
                    pageButton.classList.add('btn-active');
                }
                pageButton.onclick = function () {
                    currentPage = parseInt(this.textContent);
                    updatePagination();
                };
                pageNumbers.appendChild(pageButton);
            }
        }

        function changeEntries() {
            var entriesSelect = document.getElementById('entries');
            var selectedEntries = parseInt(entriesSelect.value);

            itemsPerPage = selectedEntries;
            currentPage = 1;
            updatePagination();
        }

        function applySearchFilter() {
            var searchInput = document.getElementById('search');
            var filter = searchInput.value.toLowerCase();
            
            filteredData = Array.from(document.querySelectorAll("table tbody tr")).filter(function (row) {
                var rowText = row.textContent.toLowerCase();
                return rowText.includes(filter);
            });

            currentPage = 1;
            updatePagination();
        }

        updatePagination();

        document.getElementById('search').addEventListener('input', applySearchFilter);

        function applyMonthYearFilter() {
            var month = document.getElementById('filterMonth').value;
            var year = document.getElementById('filterYear').value;

            filteredData = Array.from(document.querySelectorAll("table tbody tr")).filter(function (row) {
                var tanggalLembur = row.querySelector("td:nth-child(9)").textContent;
                var date = new Date(tanggalLembur);
                var rowMonth = date.getMonth() + 1;
                var rowYear = date.getFullYear();

                var monthMatch = month === '' || rowMonth == month;
                var yearMatch = year === '' || rowYear == year;

                return monthMatch && yearMatch;
            });

            currentPage = 1;
            updatePagination();
        }

    document.querySelector('form[action="{{ route('exportmad') }}"]').addEventListener('submit', function(event) {
    var month = document.getElementById('filterMonth').value;
    var year = document.getElementById('filterYear').value;

    if (month === "" || year === "") {
        alert("Silahkan pilih bulan dan tahun sebelum melakukan export");
        event.preventDefault(); // Mencegah form untuk submit
    } else {
        document.getElementById('exportMonth').value = month;
        document.getElementById('exportYear').value = year;
    }
});

    </script>

    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmModalLabel">Konfirmasi Penghapusan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus item ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tidak</button>
                    <button type="button" class="btn btn-primary" id="confirmDelete">Ya</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.getAttribute('data-id');
                document.getElementById('confirmDelete').setAttribute('data-id', itemId);
            });
        });

        document.getElementById('confirmDelete').addEventListener('click', function() {
            const itemId = this.getAttribute('data-id');
            document.getElementById('deleteForm-' + itemId).submit();
        });
    </script>

<style>

.dataTables_paginate{float:right;text-align:right;padding-top:.25em}
.paginate_button {box-sizing:border-box;
    display:inline-block;
    min-width:1.5em;
    padding:.5em 1em;
    margin-left:2px;
    text-align:center;
    text-decoration:none !important;
    cursor:pointer;color:inherit !important;
    border:1px solid transparent;
    border-radius:2px;
    background:transparent}

.dataTables_length{float:left}.dataTables_wrapper .dataTables_length select{border:1px solid #aaa;border-radius:3px;padding:5px;background-color:transparent;color:inherit;padding:4px}
.dataTables_info{clear:both;float:left;padding-top:.755em}    
.dataTables_filter{float:right;text-align:right}
.dataTables_filter input{border:1px solid #aaa;border-radius:3px;padding:5px;background-color:transparent;color:inherit;margin-left:3px}


.btn-active {
    background-color: #007bff;
    color: #fff;
}

/* Styling for paginasi container */
.dataTables_paginate {
        text-align: center;
    }

    /* Styling for each paginasi button */
 
        /* Styling for paginasi container */
    .dataTables_paginate {
        text-align: center;
    }

    /* Styling for each paginasi button */
    .paginate_button {
        display: inline-block;
        margin: 5px;
        text-align: center;
        border: 1px solid #000; 
        padding: 5px 10px;
    }

    /* Media query for small screens */
    @media (max-width: 768px) {
        .paginate_button {
            padding: 3px 6px;
        }
    }

    /* Media query for extra small screens */
    @media (max-width: 576px) {
        .dataTables_paginate {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .paginate_button {
            padding: 2px 4px;
            margin: 2px;
            
        }
    }
        
    /* Media query for small screens */
    @media (max-width: 768px) {
        .paginate_button {
            padding: 3px 6px;
        }
    }

    /* Media query for extra small screens */
    @media (max-width: 576px) {
        .dataTables_paginate {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .paginate_button {
            padding: 2px 4px;
            margin: 2px;
        }
    }

</style>
@endsection
