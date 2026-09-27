import docx
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn

def set_cell_background(cell, hex_color):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{hex_color}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = OxmlElement('w:tcMar')
    for m, val in [('top', top), ('bottom', bottom), ('left', left), ('right', right)]:
        node = OxmlElement(f'w:{m}')
        node.set(qn('w:w'), str(val))
        node.set(qn('w:type'), 'dxa')
        tcMar.append(node)
    tcPr.append(tcMar)

def create_callout(doc, text, title="INFORMASI IMPORTANT", bg_color="F9FAFB", border_color="800020"):
    tbl = doc.add_table(rows=1, cols=1)
    tbl.alignment = WD_TABLE_ALIGNMENT.CENTER
    cell = tbl.cell(0, 0)
    set_cell_background(cell, bg_color)
    set_cell_margins(cell, top=140, bottom=140, left=200, right=200)
    
    # Set left border color and width
    tcPr = cell._tc.get_or_add_tcPr()
    tcBorders = parse_xml(f'''
        <w:tcBorders {nsdecls("w")}>
            <w:top w:val="none"/>
            <w:left w:val="single" w:sz="24" w:space="0" w:color="{border_color}"/>
            <w:bottom w:val="none"/>
            <w:right w:val="none"/>
        </w:tcBorders>
    ''')
    tcPr.append(tcBorders)
    
    p = cell.paragraphs[0]
    p.paragraph_format.space_before = Pt(2)
    p.paragraph_format.space_after = Pt(4)
    run_t = p.add_run(f"📌 {title}\n")
    run_t.bold = True
    run_t.font.name = 'Arial'
    run_t.font.size = Pt(10)
    run_t.font.color.rgb = RGBColor(128, 0, 32) # Maroon
    
    run_body = p.add_run(text)
    run_body.font.name = 'Arial'
    run_body.font.size = Pt(9.5)
    run_body.font.color.rgb = RGBColor(55, 65, 81)
    
    # Empty paragraph after table for spacing
    p_space = doc.add_paragraph()
    p_space.paragraph_format.space_before = Pt(0)
    p_space.paragraph_format.space_after = Pt(6)

def build_user_manual():
    doc = Document()

    # Page Setup
    sections = doc.sections
    for section in sections:
        section.top_margin = Inches(1)
        section.bottom_margin = Inches(1)
        section.left_margin = Inches(1)
        section.right_margin = Inches(1)

    # Styles setup
    # Title
    title_p = doc.add_paragraph()
    title_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    title_p.paragraph_format.space_before = Pt(24)
    title_p.paragraph_format.space_after = Pt(6)
    
    run_title = title_p.add_run("PANDUAN PENGGUNAAN APLIKASI")
    run_title.bold = True
    run_title.font.name = 'Arial'
    run_title.font.size = Pt(24)
    run_title.font.color.rgb = RGBColor(128, 0, 32) # Brand Maroon #800020

    sub_p = doc.add_paragraph()
    sub_p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    sub_p.paragraph_format.space_after = Pt(24)
    run_sub = sub_p.add_run("Sistem Manajemen Absensi, Pengajuan Izin, Lembur, Dinas Luar & Approval\nPT Sinarta MJS")
    run_sub.font.name = 'Arial'
    run_sub.font.size = Pt(13)
    run_sub.font.color.rgb = RGBColor(163, 8, 47) # Brand Crimson #A3082F

    # Divider line
    p_div = doc.add_paragraph()
    p_div.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_div.paragraph_format.space_after = Pt(18)
    run_div = p_div.add_run("_________________________________________________________________________________")
    run_div.font.color.rgb = RGBColor(229, 231, 235)

    def add_heading_1(text):
        h = doc.add_paragraph()
        h.paragraph_format.space_before = Pt(16)
        h.paragraph_format.space_after = Pt(6)
        h.paragraph_format.keep_with_next = True
        run = h.add_run(text)
        run.bold = True
        run.font.name = 'Arial'
        run.font.size = Pt(16)
        run.font.color.rgb = RGBColor(128, 0, 32)
        return h

    def add_heading_2(text):
        h = doc.add_paragraph()
        h.paragraph_format.space_before = Pt(12)
        h.paragraph_format.space_after = Pt(4)
        h.paragraph_format.keep_with_next = True
        run = h.add_run(text)
        run.bold = True
        run.font.name = 'Arial'
        run.font.size = Pt(13)
        run.font.color.rgb = RGBColor(163, 8, 47)
        return h

    def add_body(text, bold_prefix=None):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(0)
        p.paragraph_format.space_after = Pt(4)
        p.paragraph_format.line_spacing = 1.15
        if bold_prefix:
            run_b = p.add_run(bold_prefix)
            run_b.bold = True
            run_b.font.name = 'Arial'
            run_b.font.size = Pt(10.5)
            run_b.font.color.rgb = RGBColor(31, 41, 55)
        run = p.add_run(text)
        run.font.name = 'Arial'
        run.font.size = Pt(10.5)
        run.font.color.rgb = RGBColor(55, 65, 81)
        return p

    def add_bullet(text, bold_prefix=None):
        p = doc.add_paragraph(style='List Bullet')
        p.paragraph_format.space_before = Pt(0)
        p.paragraph_format.space_after = Pt(3)
        p.paragraph_format.line_spacing = 1.15
        if bold_prefix:
            run_b = p.add_run(bold_prefix)
            run_b.bold = True
            run_b.font.name = 'Arial'
            run_b.font.size = Pt(10.5)
            run_b.font.color.rgb = RGBColor(31, 41, 55)
        run = p.add_run(text)
        run.font.name = 'Arial'
        run.font.size = Pt(10.5)
        run.font.color.rgb = RGBColor(55, 65, 81)
        return p

    # --- SECTION 1: PENDAHULUAN ---
    add_heading_1("1. PENDAHULUAN")
    add_body("Dokumen panduan ini disusun untuk memberikan petunjuk langkah demi langkah kepada seluruh pengguna (Karyawan, Atasan, dan Admin) dalam mengoperasikan Aplikasi Manajemen Karyawan Sinarta MJS. Aplikasi ini dirancang untuk mempermudah pencatatan absensi harian berbasis lokasi GPS, pengajuan izin/cuti, pengajuan lembur, pengajuan dinas luar kantor, serta proses persetujuan (approval) secara terintegrasi.")

    add_heading_2("1.1 Peran dan Hak Akses Pengguna")
    
    # Table of Roles
    table_role = doc.add_table(rows=4, cols=3)
    table_role.alignment = WD_TABLE_ALIGNMENT.CENTER
    hdr_cells = table_role.rows[0].cells
    headers = ["Peran (Role)", "Pengguna", "Hak Akses Utama"]
    widths = [Inches(1.5), Inches(1.5), Inches(3.5)]
    
    for i, h_text in enumerate(headers):
        hdr_cells[i].text = h_text
        set_cell_background(hdr_cells[i], "800020")
        set_cell_margins(hdr_cells[i], top=100, bottom=100, left=120, right=120)
        p = hdr_cells[i].paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.LEFT
        for run in p.runs:
            run.font.bold = True
            run.font.name = 'Arial'
            run.font.size = Pt(10)
            run.font.color.rgb = RGBColor(255, 255, 255)

    roles_data = [
        ("Karyawan", "Seluruh Staf / Karyawan", "Check-in & Check-out absensi lokasi GPS, pengajuan Izin/Cuti, Pengajuan Lembur, Pengajuan Dinas Luar, serta melihat riwayat pengajuan mandiri."),
        ("Atasan (Manager / Supervisor)", "Kepala Divisi / Team Lead", "Memiliki seluruh akses Karyawan + Dashboard Approval untuk mereview, menyetujui (Approve), atau menolak (Reject) pengajuan tim bawahan, serta melihat rekap absensi tim."),
        ("Admin HRD / System Admin", "Administrator & HRD", "Kelola penuh sistem, manajemen akun pengguna, atur struktur atasan-bawahan, rekapitulasi laporan absensi keseluruhan, dan cetak/export laporan.")
    ]

    for row_idx, data in enumerate(roles_data, start=1):
        row_cells = table_role.rows[row_idx].cells
        bg_c = "F9FAFB" if row_idx % 2 == 1 else "FFFFFF"
        for col_idx, cell_value in enumerate(data):
            row_cells[col_idx].text = cell_value
            set_cell_background(row_cells[col_idx], bg_c)
            set_cell_margins(row_cells[col_idx], top=80, bottom=80, left=120, right=120)
            p = row_cells[col_idx].paragraphs[0]
            for run in p.runs:
                run.font.name = 'Arial'
                run.font.size = Pt(9.5)
                run.font.color.rgb = RGBColor(55, 65, 81)

    p_space = doc.add_paragraph()
    p_space.paragraph_format.space_before = Pt(4)
    p_space.paragraph_format.space_after = Pt(4)

    add_heading_2("1.2 Persyaratan Perangkat & Browser")
    add_bullet(" Smartphone (Android / iOS) atau Laptop / PC dengan koneksi internet aktif.", "Perangkat: ")
    add_bullet(" Google Chrome, Mozilla Firefox, Microsoft Edge, atau Safari versi terbaru.", "Peramban (Browser): ")
    add_bullet(" Pengguna Wajib mengaktifkan izin GPS / Geolokasi (Location Access) pada browser agar sistem dapat mencatat titik koordinat absensi.", "Izin Akses Lokasi: ")

    create_callout(doc, "Pastikan Anda memilih 'Allow' atau 'Izinkan' saat browser meminta akses lokasi (Location Access) saat melakukan absensi. Tanpa izin lokasi, tombol Absen Masuk / Keluar tidak dapat memproses data koordinat Anda.", title="PENTING: IZIN GEOLOKASI (GPS)")

    # --- SECTION 2: PANDUAN KARYAWAN ---
    add_heading_1("2. PANDUAN PENGGUNA — MODUL KARYAWAN")
    
    add_heading_2("2.1 Autentikasi (Login & Logout)")
    add_bullet(" Buka browser Anda dan akses URL alamat aplikasi karyawan.", "1. Masuk Alamat URL: ")
    add_bullet(" Masukkan Email terdaftar dan Password akun Anda pada form Login.", "2. Form Login: ")
    add_bullet(" Klik tombol Login. Sistem akan mengarahkan Karyawan ke halaman Absensi Utama, atau Atasan ke Halaman Approval.", "3. Berhasil Masuk: ")
    add_bullet(" Untuk keluar dari sistem, klik menu nama profil di sudut kanan atas lalu pilih 'Log Out'.", "4. Keluar (Logout): ")

    add_heading_2("2.2 Melakukan Absensi Harian (Check-in & Check-out)")
    add_body("Fitur absensi digunakan untuk mencatat kehadiran harian karyawan secara realtime beserta titik lokasi koordinat Google Maps.")
    add_bullet(" Masuk ke menu 'Absensi'. Halaman akan menampilkan status absensi hari ini.", "Langkah 1: ")
    add_bullet(" Apabila Anda belum absen masuk, klik tombol 'Absen Masuk' (tombol berwarna Maroon).", "Langkah 2 (Absen Masuk): ")
    add_bullet(" Browser akan meminta koordinat GPS Anda secara otomatis. Tunggu beberapa detik hingga proses pencatatan selesai.", "Langkah 3 (Verifikasi Lokasi): ")
    add_bullet(" Setelah berhasil, waktu masuk akan terบันทึก (tercatat) dan status absensi akan berubah (misal: 'Hadir' atau 'Telat').", "Langkah 4: ")
    add_bullet(" Saat jam kerja berakhir, buka kembali menu 'Absensi' dan klik tombol 'Absen Keluar'.", "Langkah 5 (Absen Keluar): ")
    add_bullet(" Di bagian bawah halaman Absensi, Anda dapat melihat tabel 'Riwayat Bulan Ini' yang menampilkan tanggal, jam masuk, jam keluar, status (Hadir/Telat/Izin/Alpha), serta tautan 'Lihat Lokasi' pada Google Maps.", "Langkah 6 (Riwayat Absensi): ")

    create_callout(doc, "Tanda Badge Status Absensi:\n• Hijau (Hadir): Absen tepat waktu.\n• Kuning (Telat): Absen melewati batas toleransi masuk.\n• Biru (Izin): Memiliki izin/dinas yang telah disetujui.\n• Merah (Alpha): Tidak ada catatan kehadiran atau izin.", title="INFORMASI STATUS ABSENSI", border_color="EAB308")

    add_heading_2("2.3 Pengajuan Izin / Cuti")
    add_body("Karyawan dapat mengajukan izin tidak masuk kerja, cuti tahunan, sakit, atau alasan penting lainnya secara online.")
    add_bullet(" Akses menu 'Pengajuan' -> pilih 'Izin'.", "1. Navigasi: ")
    add_bullet(" Klik tombol '+ Buat Pengajuan Izin'.", "2. Buat Form: ")
    add_bullet(" Isi Jenis Izin (Cuti, Sakit, Alasan Penting), Tanggal Mulai, Tanggal Selesai, serta Alasan/Keterangan lengkap.", "3. Pengisian Data: ")
    add_bullet(" Klik tombol 'Kirim Pengajuan'. Status pengajuan akan menjadi 'Pending' dan otomatis terkirim ke Atasan Anda untuk direview.", "4. Kirim: ")

    add_heading_2("2.4 Pengajuan Lembur")
    add_body("Digunakan ketika Karyawan melaksanakan tugas tambahan di luar jam kerja efektif.")
    add_bullet(" Akses menu 'Pengajuan' -> pilih 'Lembur'.", "1. Navigasi: ")
    add_bullet(" Klik tombol '+ Buat Pengajuan Lembur'.", "2. Buat Form: ")
    add_bullet(" Tentukan Tanggal Lembur, Jam Mulai, Jam Selesai, dan Deskripsi Pekerjaan yang dikerjakan.", "3. Form Lembur: ")
    add_bullet(" Simpan pengajuan dan pantau status persetujuan dari Atasan pada tabel Riwayat Lembur.", "4. Simpan: ")

    add_heading_2("2.5 Pengajuan Dinas Luar Kantor")
    add_body("Digunakan bagi Karyawan yang mendapatkan tugas perjalanan dinas atau kunjungan kerja di luar lokasi kantor utama.")
    add_bullet(" Buka menu 'Pengajuan' -> pilih 'Dinas Luar'.", "1. Navigasi: ")
    add_bullet(" Klik '+ Buat Pengajuan Dinas'.", "2. Buat Form: ")
    add_bullet(" Masukkan Nama Tugas/Kegiatan, Tanggal Berangkat, Tanggal Kembali, Lokasi Tujuan Dinas, dan Keterangan Tugas.", "3. Detail Dinas: ")
    add_bullet(" Kirim pengajuan untuk verifikasi Atasan.", "4. Kirim: ")

    # --- SECTION 3: PANDUAN ATASAN & ADMIN ---
    add_heading_1("3. PANDUAN PENGGUNA — MODUL ATASAN & ADMIN")

    add_heading_2("3.1 Dashboard Approval (Persetujuan Tim)")
    add_body("Atasan (Manager / Supervisor) memiliki akses ke Dashboard Approval untuk mengelola pengajuan dari seluruh anggota tim bawahan.")
    add_bullet(" Masuk ke menu 'Approval' dari navigasi utama.", "1. Akses Dashboard Approval: ")
    add_bullet(" Halaman utama menampilkan kartu statistik pengajuan (Pending Izin, Pending Lembur, Pending Dinas) serta tabel daftar pengajuan yang membutuhkan keputusan persetujuan.", "2. Ringkasan Pengajuan: ")

    add_heading_2("3.2 Memproses Persetujuan (Approve / Reject)")
    add_bullet(" Periksa data pemohon (Nama Karyawan, Jenis Pengajuan, Tanggal, dan Alasan).", "1. Peninjauan Data: ")
    add_bullet(" Klik tombol 'Setujui' (warna Hijau/Accent) untuk menyetujui pengajuan. Status pengajuan otomatis berubah menjadi 'Approved'.", "2. Menyetujui (Approve): ")
    add_bullet(" Klik tombol 'Tolak' (warna Merah) jika pengajuan tidak sesuai atau ditolak. Status berubah menjadi 'Rejected'.", "3. Menolak (Reject): ")

    create_callout(doc, "Setiap pengajuan yang telah di-Approve atau di-Reject akan otomatis terekam dalam sistem dan statistik absensi/lembur karyawan bersangkutan akan terbarui.", title="CATATAN SISTEM APPROVAL")

    add_heading_2("3.3 Rekap Absensi Tim & Laporan Visualisasi Analytics")
    add_bullet(" Tab 'Rekap Absensi': Menampilkan daftar absensi harian seluruh bawahan beserta filter tanggal, jam masuk, jam keluar, dan koordinat peta lokasi.", "1. Rekap Absensi Tim: ")
    add_bullet(" Tab 'Laporan Visualisasi': Menyajikan grafik ringkasan tingkat kehadiran tim, persentase keterlambatan, serta perbandingan jumlah izin dan lembur bulanan.", "2. Visualisasi Laporan: ")
    add_bullet(" Tab 'Rekap Pengajuan': Menampilkan seluruh riwayat pengajuan (Izin, Lembur, Dinas) yang telah diproses sebelumnya.", "3. Riwayat Keseluruhan: ")

    # --- SECTION 4: FAQ & TROUBLESHOOTING ---
    add_heading_1("4. PERTANYAAN UMUM & SOLUSI MASALAH (FAQ)")

    table_faq = doc.add_table(rows=4, cols=2)
    table_faq.alignment = WD_TABLE_ALIGNMENT.CENTER
    hdr_faq = table_faq.rows[0].cells
    hdr_faq[0].text = "Permasalahan / Pertanyaan"
    hdr_faq[1].text = "Solusi / Langkah Penanganan"
    
    set_cell_background(hdr_faq[0], "800020")
    set_cell_background(hdr_faq[1], "800020")
    set_cell_margins(hdr_faq[0], top=100, bottom=100, left=120, right=120)
    set_cell_margins(hdr_faq[1], top=100, bottom=100, left=120, right=120)
    
    for c in hdr_faq:
        p = c.paragraphs[0]
        for run in p.runs:
            run.font.bold = True
            run.font.name = 'Arial'
            run.font.size = Pt(10)
            run.font.color.rgb = RGBColor(255, 255, 255)

    faqs = [
        ("Pesan Error: 'Gagal mengambil lokasi. Pastikan izin lokasi diaktifkan.' saat absen.",
         "1. Buka Pengaturan Browser (Browser Settings) -> Privacy and Security -> Site Settings -> Location.\n2. Pastikan alamat web aplikasi diizinkan (Allowed) mengakses lokasi.\n3. Pastikan GPS/Location Service pada smartphone atau laptop Anda aktif.\n4. Refresh halaman web dan coba klik tombol Absen kembali."),
        
        ("Pengajuan Izin / Lembur salah tanggal atau ingin diubah.",
         "Pengajuan yang masih berstatus 'Pending' dapat dikoordinasikan dengan Atasan langsung agar ditolak (Reject), kemudian Karyawan dapat membuat pengajuan baru yang benar."),
        
        ("Bagaimana jika lupa password akun?",
         "Hubungi Administrator IT / HRD untuk melakukan reset password akun Anda ke password standar awal.")
    ]

    for idx, (q, a) in enumerate(faqs, start=1):
        r_cells = table_faq.rows[idx].cells
        bg_c = "F9FAFB" if idx % 2 == 1 else "FFFFFF"
        
        r_cells[0].text = q
        r_cells[1].text = a
        
        set_cell_background(r_cells[0], bg_c)
        set_cell_background(r_cells[1], bg_c)
        set_cell_margins(r_cells[0], top=80, bottom=80, left=120, right=120)
        set_cell_margins(r_cells[1], top=80, bottom=80, left=120, right=120)
        
        for cell in r_cells:
            p = cell.paragraphs[0]
            for run in p.runs:
                run.font.name = 'Arial'
                run.font.size = Pt(9.5)
                run.font.color.rgb = RGBColor(55, 65, 81)

    # --- SECTION 5: DUKUNGAN LAYANAN ---
    add_heading_1("5. INFORMASI KONTAK & BANTUAN")
    add_body("Apabila Anda mengalami kendala teknis atau membutuhkan bantuan lebih lanjut mengenai penggunaan aplikasi, silakan menghubungi tim berikut:")
    add_bullet(" hrd@sinarta-mjs.co.id", "Email Tim HRD: ")
    add_bullet(" it-support@sinarta-mjs.co.id", "Email Tim IT Support: ")
    add_bullet(" Senin - Jumat (08:00 - 17:00 WIB)", "Jam Operasional Bantuan: ")

    doc.save("Panduan_Penggunaan_Aplikasi_Karyawan.docx")
    print("Dokumen berhasil dibuat: Panduan_Penggunaan_Aplikasi_Karyawan.docx")

if __name__ == '__main__':
    build_user_manual()
