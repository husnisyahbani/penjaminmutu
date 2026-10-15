/* PTK (Permintaan Tindakan Koreksi) - halaman auditor.
 *
 * Satu baris = satu butir tilik yang sudah dinilai auditor (OB / TS MINOR /
 * TS MAYOR) pada daftar tilik yang dibuat auditor yang sedang masuk.
 * Rencana koreksi hanya DITAMPILKAN di sini; yang menulis tetap auditee.
 *
 * Data mengikuti saringan periode pada kepala panel (bawaan: periode aktif).
 */
$(function () {

    var daftarptk = $('#ptk').DataTable({
        "responsive": false,
        /* Lebar kolom diatur CSS (persentase + table-layout fixed pada
           assets/app/ptk.css), bukan dihitung DataTables dari isi sel. */
        "autoWidth": false,
        "processing": true,
        "serverSide": true,
        "searching": true,
        "order": [],
        "columnDefs": [
            /* Kolom No, Rencana Koreksi & Status tidak dapat diurutkan. */
            {"targets": [0, 8, 9], "orderable": false},
            /* Kelas kolom diambil dari header (lihat assets/app/ptk.css)
               supaya lebarnya proporsional. */
            {"targets": 0, "className": "ptk-kolom-no"},
            {"targets": 1, "className": "ptk-kolom-formulir"},
            {"targets": 2, "className": "ptk-kolom-auditee"},
            {"targets": 3, "className": "ptk-kolom-unit"},
            {"targets": 4, "className": "ptk-kolom-butir"},
            {"targets": 5, "className": "ptk-kolom-hasil"},
            {"targets": 6, "className": "ptk-kolom-temuan"},
            {"targets": 7, "className": "ptk-kolom-catatan"},
            {"targets": 8, "className": "ptk-kolom-koreksi"},
            {"targets": 9, "className": "ptk-kolom-status"}
        ],
        "ajax": {
            "url": base_url + "/ptk/listptk/",
            "type": "POST",
            /* Saringan periode ikut dikirim; kosong = semua periode. */
            "data": function (d) {
                d.periode_id = $('#filter_periode').val();
            }
        },
        "createdRow": function (baris) {
            $(baris).find('.ptk-klamp').each(function () {
                var teks = $(this).text();
                if (teks.length > 140) {
                    $(this).attr('title', teks);
                }
            });
        },
        "drawCallback": function () {
            $('#ptk .ptk-status-select').each(function () {
                aturWarnaSelect($(this), $(this).val());
            });
        }
    });

    /* Kotak cari pada kepala panel (menggantikan kotak bawaan DataTables,
       lihat aturan .ptk-tabel-kotak .dataTables_filter pada ptk.css). */
    $('#cari_ptk').on('input', function () {
        daftarptk.search(String(this.value || '').trim()).draw();
    });

    /* Saringan periode pada kepala panel: bawaan = periode aktif (dipilih
       di halaman), kosong = semua periode. Tabel dimuat ulang dan angka
       kartu statistik ikut diperbarui. */
    $('#filter_periode').on('change', function () {
        daftarptk.ajax.reload();
        perbaruiKartu($(this).val());
    });

    function angka(nilai) {
        return String(nilai === null || nilai === undefined ? 0 : nilai)
            .replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    /* Batang kemajuan kartu: persen null -> batang disembunyikan. */
    function aturBar(id, persen, ket) {
        if (persen === null) {
            $('#' + id + '_bar_kotak').hide();
            $('#' + id + '_bar_ket').hide().text('');
            return;
        }
        $('#' + id + '_bar_kotak').show().attr('aria-label', ket);
        $('#' + id + '_bar').css('width', persen + '%');
        $('#' + id + '_bar_ket').show().text(ket);
    }

    /* Angka kartu statistik untuk periode terpilih (ptk/statistik). */
    function perbaruiKartu(periode_id) {
        $.ajax({
            url: base_url + "/ptk/statistik",
            type: "POST",
            dataType: "json",
            data: {periode_id: periode_id}
        }).done(function (data) {
            if (!data || !data.status) {
                return;
            }

            var total    = parseInt(data.total, 10) || 0;
            var observasi = parseInt(data.observasi, 10) || 0;
            var minor    = parseInt(data.minor, 10) || 0;
            var mayor    = parseInt(data.mayor, 10) || 0;
            var sudah    = parseInt(data.sudah_koreksi, 10) || 0;
            var sisa     = parseInt(data.tanpa_koreksi, 10) || 0;

            function persen(nilai) {
                return total > 0 ? Math.round(nilai * 100 / total) : null;
            }

            $('#stat_total').text(angka(total));
            $('#stat_observasi').text(angka(observasi));
            $('#stat_minor').text(angka(minor));
            $('#stat_mayor').text(angka(mayor));

            $('#stat_total_ket').text(total > 0
                ? sudah + ' sudah ditindaklanjuti, ' + sisa + ' belum'
                : 'Belum ada temuan pada daftar tilik Anda');

            var p_total = total > 0 ? Math.round(sudah * 100 / total) : null;
            aturBar('stat_total', p_total,
                p_total === null ? '' : p_total + '% sudah ada rencana koreksi');

            var kategori = [
                {id: 'stat_observasi', nilai: observasi},
                {id: 'stat_minor', nilai: minor},
                {id: 'stat_mayor', nilai: mayor}
            ];
            $.each(kategori, function (i, k) {
                var p = persen(k.nilai);
                aturBar(k.id, p, p === null ? '' : p + '% dari total temuan');
            });
        });
    }

    /* Warna latar & teks select status mengikuti nilai yang dipilih. */
    function aturWarnaSelect($sel, status) {
        $sel.removeClass('ptk-status--open ptk-status--proses ptk-status--closed ptk-status--kosong');
        var st = String(status || '').toUpperCase();
        if (st === 'CLOSED') {
            $sel.addClass('ptk-status--closed');
        } else if (st === 'PROSES') {
            $sel.addClass('ptk-status--proses');
        } else if (st === 'OPEN') {
            $sel.addClass('ptk-status--open');
        } else {
            $sel.addClass('ptk-status--kosong');
        }
    }

    /* Auditor memilih status temuan (OPEN / PROSES / CLOSED). */
    $(document).on('change', '.ptk-status-select', function () {
        var $sel = $(this);
        var dtjwb_id = $sel.data('dtjwb_id');
        var statusBaru = $sel.val();
        var statusLama = $sel.data('sebelumnya') || '';

        var teksTanya = statusBaru
            ? "Ubah status temuan ini menjadi " + statusBaru + "?"
            : "Kosongkan status temuan ini?";

        swal.fire({
            title: "Ubah Status PTK?",
            text: teksTanya,
            type: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Simpan!",
            cancelButtonText: "Batal",
            showLoaderOnConfirm: true,
            preConfirm: function () {
                return $.ajax({
                    url: base_url + "/ptk/updatestatus",
                    type: "POST",
                    dataType: "json",
                    data: {
                        dtjwb_id: dtjwb_id,
                        status: statusBaru
                    }
                }).then(function (res) {
                    return res;
                }).fail(function () {
                    swal.fire("Oops", "Gagal menghubungi server!", "error");
                });
            }
        }).then(function (result) {
            if (result && (result.value || result === true)) {
                var res = result.value || result;
                if (res && res.status) {
                    $sel.data('sebelumnya', statusBaru);
                    aturWarnaSelect($sel, statusBaru);
                    swal.fire({
                        title: "Berhasil",
                        text: "Status PTK berhasil diperbarui.",
                        type: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    swal.fire("Gagal", (res && res.pesan) ? res.pesan : "Gagal mengubah status.", "error");
                    $sel.val(statusLama);
                    aturWarnaSelect($sel, statusLama);
                }
            } else {
                $sel.val(statusLama);
                aturWarnaSelect($sel, statusLama);
            }
        });
    });

});

