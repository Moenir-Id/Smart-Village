<?php
/**
 * LaporanPdfService — Professional Landscape Report
 * Layout: A4 Landscape, formal government/corporate style
 *
 * Dependency: composer require tecnickcom/tcpdf
 * Place at: app/Services/LaporanPdfService.php
 */

namespace App\Services;

use TCPDF;
use Illuminate\Support\Collection;

class LaporanPdfService
{
    // ─── Palet ───────────────────────────────────────────────────────────────
    private const C_KOP_TOP     = [30,  58, 138];   // #1e3a8a  — navy tua (kop atas)
    private const C_KOP_BOT     = [37,  99, 235];   // #2563eb  — biru (kop bawah)
    private const C_HEADER_TXT  = [255, 255, 255];
    private const C_PRIMARY     = [30,  58, 138];   // #1e3a8a
    private const C_PRIMARY_M   = [59, 130, 246];   // #3b82f6
    private const C_PRIMARY_L   = [219,234, 254];   // #dbeafe
    private const C_SUCCESS     = [21, 128,  61];
    private const C_SUCCESS_L   = [220,252, 231];
    private const C_WARNING     = [146, 64,  14];
    private const C_WARNING_L   = [254,243, 199];
    private const C_DANGER      = [185, 28,  28];
    private const C_DANGER_L    = [254,226, 226];
    private const C_INFO        = [7,  89, 133];
    private const C_INFO_L      = [224,242, 254];
    private const C_DARK        = [15,  23,  42];
    private const C_GRAY_D      = [51,  65,  85];
    private const C_GRAY        = [100,116, 139];
    private const C_GRAY_L      = [241,245, 249];
    private const C_STRIPE      = [248,250, 252];
    private const C_BORDER      = [203,213, 225];
    private const C_BORDER_L    = [226,232, 240];
    private const C_WHITE       = [255,255, 255];
    private const C_GOLD        = [161,120,   0];   // garis aksen kop

    private const STATUS_MAP = [
        'selesai'  => ['label' => 'Selesai',  'color' => self::C_SUCCESS, 'bg' => self::C_SUCCESS_L],
        'pending'  => ['label' => 'Menunggu', 'color' => self::C_WARNING, 'bg' => self::C_WARNING_L],
        'diproses' => ['label' => 'Diproses', 'color' => self::C_INFO,    'bg' => self::C_INFO_L],
        'ditolak'  => ['label' => 'Ditolak',  'color' => self::C_DANGER,  'bg' => self::C_DANGER_L],
    ];

    // Page dimensions (A4 Landscape)
    private float $PW = 297.0;
    private float $PH = 210.0;
    private float $LM = 15.0;
    private float $RM = 15.0;
    private float $TM = 14.0;
    private float $BM = 14.0;

    // ─── Entry Point ─────────────────────────────────────────────────────────
    public static function generate(
        Collection $permohonan,
        array      $stats,
        Collection $perJenis,
        array      $harian,
        ?float     $avgSla,
        string     $namaInstansi,
        string     $alamat,
        string     $bulanLabel,
        string     $bulan,
        string     $dicetak,
        string     $kepalaDesaNama = '',
        string     $kepalaDesaNip  = '',
        string     $logoAbsPath    = '',
    ): string {
        $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

        $pdf->SetCreator('Sistem Permohonan Surat');
        $pdf->SetAuthor($namaInstansi);
        $pdf->SetTitle("Laporan Permohonan Surat - $bulanLabel");
        $pdf->SetSubject('Laporan Bulanan Permohonan Surat');

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 14, 15);
        $pdf->SetAutoPageBreak(true, 16);
        $pdf->AddPage();

        $self = new self();

        $self->drawKop($pdf, $namaInstansi, $alamat, $bulanLabel, $dicetak, $logoAbsPath);
        $self->drawSummaryStrip($pdf, $stats, $avgSla);
        $self->drawBody($pdf, $harian, $perJenis, $stats);
        $self->drawPermohonanTable($pdf, $permohonan, $stats['total'], $bulanLabel);
        $self->drawFooter($pdf, $namaInstansi, $bulanLabel, $kepalaDesaNama, $kepalaDesaNip);

        return $pdf->Output('', 'S');
    }

    // ─── 1. KOP SURAT ────────────────────────────────────────────────────────
    private function drawKop(TCPDF $pdf, string $nama, string $alamat, string $bulanLabel, string $dicetak, string $logoAbsPath = ''): void
    {
        $lm   = 15;
        $w    = 267;
        $y0   = 14;
        $kopH = 26;

        // BG navy solid
        $pdf->SetFillColor(...self::C_KOP_TOP);
        $pdf->Rect($lm, $y0, $w, $kopH, 'F');

        // Garis aksen emas di bawah kop
        $pdf->SetFillColor(...self::C_GOLD);
        $pdf->Rect($lm, $y0 + $kopH, $w, 1.2, 'F');

        // ── Logo (jika ada) ──
        $logoW    = 0;
        $logoPad  = 0;
        if ($logoAbsPath && file_exists($logoAbsPath)) {
            $logoSize = 18; // mm, kotak logo
            $logoX    = $lm + 5;
            $logoY    = $y0 + ($kopH - $logoSize) / 2;
            try {
                $pdf->Image($logoAbsPath, $logoX, $logoY, $logoSize, $logoSize, '', '', '', true, 150, '', false, false, 0);
                $logoW   = $logoSize;
                $logoPad = 4; // jarak antara logo dan teks
            } catch (\Exception $e) {
                $logoW = 0; $logoPad = 0;
            }
        }

        // Batas mulai teks identitas
        $textX    = $lm + ($logoW > 0 ? $logoW + $logoPad + 5 : 6);
        $textMaxW = $w * 0.58 - ($logoW + $logoPad);

        // Nama desa
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(...self::C_HEADER_TXT);
        $pdf->SetXY($textX, $y0 + 4.5);
        $pdf->Cell($textMaxW, 8, mb_strtoupper($nama), 0, 0, 'L');

        // Alamat
        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->SetTextColor(195, 218, 255);
        $pdf->SetXY($textX, $y0 + 13);
        $pdf->Cell($textMaxW, 4.5, $alamat, 0, 0, 'L');

        // Kanan: judul laporan + periode + dicetak
        $rightX = $lm + $w * 0.58;
        $rightW = $w * 0.42;

        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->SetTextColor(...self::C_HEADER_TXT);
        $pdf->SetXY($rightX, $y0 + 4);
        $pdf->Cell($rightW, 8, 'LAPORAN PERMOHONAN SURAT', 0, 0, 'R');

        $pdf->SetFont('helvetica', '', 7.5);
        $pdf->SetTextColor(195, 218, 255);
        $pdf->SetXY($rightX, $y0 + 13);
        $pdf->Cell($rightW, 4.5, "Periode: $bulanLabel", 0, 0, 'R');

        $pdf->SetXY($rightX, $y0 + 18);
        $pdf->Cell($rightW, 4, "Dicetak: $dicetak", 0, 0, 'R');

        $pdf->SetY($y0 + $kopH + 3.5);
    }

    // ─── 2. SUMMARY STRIP ────────────────────────────────────────────────────
    // Bukan card besar — cukup 1 baris ringkas horizontal
    private function drawSummaryStrip(TCPDF $pdf, array $stats, ?float $avgSla): void
    {
        $lm  = 15;
        $w   = 267;
        $y   = $pdf->GetY();
        $h   = 12;

        // Background strip abu sangat terang
        $pdf->SetFillColor(...self::C_GRAY_L);
        $pdf->SetDrawColor(...self::C_BORDER);
        $pdf->SetLineWidth(0.3);
        $pdf->RoundedRect($lm, $y, $w, $h, 2, '1111', 'DF');

        // Pembagi
        $items = [
            ['Total Permohonan',   $stats['total'],    self::C_PRIMARY],
            ['Menunggu',           $stats['pending'],  self::C_WARNING],
            ['Diproses',           $stats['diproses'], self::C_INFO],
            ['Selesai',            $stats['selesai'],  self::C_SUCCESS],
            ['Ditolak',            $stats['ditolak'],  self::C_DANGER],
        ];

        if ($avgSla !== null) {
            $items[] = ['Rata-rata SLA', sprintf('%.1f jam', $avgSla), self::C_GRAY_D];
        }

        $count   = count($items);
        $colW    = $w / $count;
        $x       = $lm;

        foreach ($items as $i => [$label, $value, $color]) {
            // Garis pemisah vertikal (kecuali pertama)
            if ($i > 0) {
                $pdf->SetDrawColor(...self::C_BORDER);
                $pdf->SetLineWidth(0.25);
                $pdf->Line($x, $y + 2, $x, $y + $h - 2);
            }

            // Angka
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->SetTextColor(...$color);
            $pdf->SetXY($x, $y + 0.5);
            $pdf->Cell($colW, 7, (string)$value, 0, 0, 'C');

            // Label
            $pdf->SetFont('helvetica', '', 6.5);
            $pdf->SetTextColor(...self::C_GRAY);
            $pdf->SetXY($x, $y + 6.5);
            $pdf->Cell($colW, 4.5, $label, 0, 0, 'C');

            $x += $colW;
        }

        $pdf->SetY($y + $h + 5);
    }

    // ─── 3. BODY: Chart + Per Jenis ──────────────────────────────────────────
    private function drawBody(TCPDF $pdf, array $harian, Collection $perJenis, array $stats): void
    {
        $lm      = 15;
        $w       = 267;
        $y       = $pdf->GetY();
        $leftW   = $w * 0.54;
        $rightW  = $w * 0.43;
        $gap     = $w * 0.03;
        $rightX  = $lm + $leftW + $gap;

        // ── Panel kiri: Chart Harian ──
        $panelH  = 46;
        $this->panelHeader($pdf, 'Grafik Pengajuan Harian', $lm, $y, $leftW);
        $chartY  = $y + 7;

        // BG chart
        $pdf->SetFillColor(252, 253, 255);
        $pdf->SetDrawColor(...self::C_BORDER_L);
        $pdf->SetLineWidth(0.25);
        $pdf->RoundedRect($lm, $chartY, $leftW, $panelH, 0, '0011', 'DF');

        // Gridlines
        $plotH   = $panelH - 10;
        $plotY0  = $chartY + 4;
        $plotX0  = $lm + 6;
        $plotW   = $leftW - 12;

        $pdf->SetDrawColor(...self::C_BORDER_L);
        $pdf->SetLineWidth(0.15);
        for ($gl = 1; $gl <= 4; $gl++) {
            $gy = $plotY0 + $plotH * (1 - $gl / 4);
            $pdf->Line($plotX0, $gy, $plotX0 + $plotW, $gy);
        }

        // Bars
        $vals = [];
        for ($d = 1; $d <= 31; $d++) {
            $vals[$d] = $harian[str_pad($d, 2, '0', STR_PAD_LEFT)] ?? 0;
        }
        $maxV   = max(array_values($vals)) ?: 1;
        $barGap = 0.5;
        $barW   = ($plotW - 30 * $barGap) / 31;

        foreach ($vals as $d => $v) {
            $bx  = $plotX0 + ($d - 1) * ($barW + $barGap);
            $bh  = $v > 0 ? max(1.5, ($v / $maxV) * ($plotH - 2)) : 0;
            $by  = $plotY0 + $plotH - $bh;

            if ($v > 0) {
                $pdf->SetFillColor(...self::C_PRIMARY_M);
                $pdf->Rect($bx, $by + 1.5, $barW, $bh - 1.5, 'F');
                $pdf->SetFillColor(...self::C_PRIMARY);
                $pdf->RoundedRect($bx, $by, $barW, min($bh, 2.5), 0.7, '1100', 'F');

                if ($bh > 8) {
                    $pdf->SetFont('helvetica', 'B', 4.5);
                    $pdf->SetTextColor(...self::C_PRIMARY);
                    $pdf->SetXY($bx - 0.5, $by - 3.5);
                    $pdf->Cell($barW + 1, 3.5, (string)$v, 0, 0, 'C');
                }
            } else {
                $pdf->SetFillColor(...self::C_BORDER_L);
                $pdf->Rect($bx, $plotY0 + $plotH - 1, $barW, 1, 'F');
            }

            if ($d % 5 === 0) {
                $pdf->SetFont('helvetica', '', 5);
                $pdf->SetTextColor(...self::C_GRAY);
                $pdf->SetXY($bx - 1.5, $plotY0 + $plotH + 1);
                $pdf->Cell($barW + 3, 3.5, (string)$d, 0, 0, 'C');
            }
        }

        // ── Panel kanan: Per Jenis Surat ──
        $this->panelHeader($pdf, 'Rekapitulasi Per Jenis Surat', $rightX, $y, $rightW);
        $tblY   = $y + 7;

        // Header tabel
        $colW4  = [$rightW * 0.52, $rightW * 0.17, $rightW * 0.17, $rightW * 0.14];
        $heads  = ['Jenis Surat', 'Masuk', 'Selesai', '%'];

        $pdf->SetFillColor(...self::C_PRIMARY);
        $pdf->RoundedRect($rightX, $tblY, $rightW, 7, 0, '0011', 'F');
        $pdf->SetFont('helvetica', 'B', 7.5);
        $pdf->SetTextColor(...self::C_WHITE);
        $tx = $rightX;
        foreach ($heads as $i => $h) {
            $pdf->SetXY($tx + ($i === 0 ? 2.5 : 0), $tblY + 0.5);
            $pdf->Cell($colW4[$i], 6, $h, 0, 0, $i === 0 ? 'L' : 'C');
            $tx += $colW4[$i];
        }
        $rowY = $tblY + 7;

        if ($perJenis->isEmpty()) {
            $pdf->SetFont('helvetica', 'I', 8);
            $pdf->SetTextColor(...self::C_GRAY);
            $pdf->SetXY($rightX, $rowY + 3);
            $pdf->Cell($rightW, 7, 'Belum ada data', 0, 0, 'C');
            $rowY += 10;
        }

        foreach ($perJenis as $ji => $jdata) {
            $total   = $jdata['total'] ?? 0;
            $selesai = $jdata['selesai'] ?? 0;
            $pct     = $total > 0 ? round($selesai / $total * 100) : 0;
            $bg      = $ji % 2 === 0 ? self::C_WHITE : self::C_STRIPE;
            $pctClr  = $pct >= 80 ? self::C_SUCCESS : ($pct >= 50 ? self::C_WARNING : self::C_DANGER);
            $namaLabel = is_string($ji) ? $ji : ($jdata['nama'] ?? '-');

            $linesN = $pdf->getNumLines($namaLabel, $colW4[0] - 5);
            $rH     = max(7, $linesN * 4.3 + 2.5);

            $pdf->SetFillColor(...$bg);
            $pdf->Rect($rightX, $rowY, $rightW, $rH, 'F');

            $pdf->SetFont('helvetica', '', 7.5);
            $pdf->SetTextColor(...self::C_DARK);
            $pdf->SetXY($rightX + 2.5, $rowY + 1);
            $pdf->MultiCell($colW4[0] - 4, 4.3, $namaLabel, 0, 'L', false, 0);
            $tx = $rightX + $colW4[0];

            $pdf->SetXY($tx, $rowY);
            $pdf->Cell($colW4[1], $rH, (string)$total, 0, 0, 'C');
            $tx += $colW4[1];

            $pdf->SetXY($tx, $rowY);
            $pdf->Cell($colW4[2], $rH, (string)$selesai, 0, 0, 'C');
            $tx += $colW4[2];

            $pdf->SetFont('helvetica', 'B', 7.5);
            $pdf->SetTextColor(...$pctClr);
            $pdf->SetXY($tx, $rowY);
            $pdf->Cell($colW4[3], $rH, "$pct%", 0, 0, 'C');

            $pdf->SetDrawColor(...self::C_BORDER_L);
            $pdf->SetLineWidth(0.15);
            $pdf->Line($rightX, $rowY + $rH, $rightX + $rightW, $rowY + $rH);

            $rowY += $rH;
        }

        $bottomChart = $y + 7 + $panelH + 4;
        $pdf->SetY(max($bottomChart, $rowY) + 5);
    }

    // ─── 4. TABEL PERMOHONAN ─────────────────────────────────────────────────
    private function drawPermohonanTable(TCPDF $pdf, Collection $permohonan, int $total, string $bulanLabel): void
    {
        $lm = 15;
        $w  = 267;

        // Divider
        $pdf->SetDrawColor(...self::C_BORDER);
        $pdf->SetLineWidth(0.35);
        $pdf->Line($lm, $pdf->GetY(), $lm + $w, $pdf->GetY());
        $pdf->Ln(3.5);

        $this->panelHeader($pdf, "Daftar Permohonan Surat — $bulanLabel  ($total data)", $lm, $pdf->GetY(), $w);
        $pdf->Ln(2);

        // Kolom — total harus = $w = 267
        // 267 * proporsi = nilai mm
        $cols = [
            'No'          => $w * 0.038,   //  ~10.1
            'Kode'        => $w * 0.082,   //  ~21.9
            'Nama'        => $w * 0.140,   //  ~37.4
            'NIK'         => $w * 0.090,   //  ~24.0
            'Jenis Surat' => $w * 0.160,   //  ~42.7
            'Keperluan'   => $w * 0.155,   //  ~41.4
            'Status'      => $w * 0.082,   //  ~21.9
            'Tgl Masuk'   => $w * 0.090,   //  ~24.0
            'Tgl Selesai' => $w * 0.083,   //  ~22.2
            'Petugas'     => $w * 0.080,   //  ~21.4
        ];
        // total = 1.000 × 267 ✓

        $this->drawTableHeader($pdf, $cols, $lm);

        foreach ($permohonan as $i => $p) {
            $this->drawTableRow($pdf, $p, $i + 1, $cols, $lm, $total);
        }

        if ($permohonan->isEmpty()) {
            $pdf->SetFont('helvetica', 'I', 9);
            $pdf->SetTextColor(...self::C_GRAY);
            $pdf->SetFillColor(...self::C_STRIPE);
            $pdf->Cell($w, 12, 'Tidak ada permohonan bulan ini.', 0, 1, 'C', true);
        }
    }

    private function drawTableHeader(TCPDF $pdf, array $cols, float $lm): void
    {
        $hH    = 8.5;
        $y     = $pdf->GetY();
        $totalW = array_sum($cols);

        // BG header
        $pdf->SetFillColor(...self::C_KOP_TOP);
        $pdf->Rect($lm, $y, $totalW, $hH, 'F');

        $pdf->SetFont('helvetica', 'B', 6.8);
        $pdf->SetTextColor(...self::C_WHITE);
        $x = $lm;
        foreach ($cols as $label => $cw) {
            $align = in_array($label, ['No','Status','Tgl Masuk','Tgl Selesai','NIK']) ? 'C' : 'L';
            $px    = $align === 'L' ? 2 : 0;
            $pdf->SetXY($x + $px, $y + 1);
            $pdf->Cell($cw - $px, $hH - 2, $label, 0, 0, $align);
            $x += $cw;
        }
        $pdf->SetY($y + $hH);
    }

    private function drawTableRow(TCPDF $pdf, $p, int $no, array $cols, float $lm, int $total): void
    {
        $status    = $p->status ?? 'pending';
        $statusCfg = self::STATUS_MAP[$status] ?? ['label' => ucfirst($status), 'color' => self::C_GRAY, 'bg' => self::C_GRAY_L];
        $bg        = $no % 2 === 0 ? self::C_STRIPE : self::C_WHITE;

        $nama      = $p->nama_lengkap ?? '-';
        $nik       = $p->nik ?? '-';
        $jenis     = $p->jenisSurat->nama ?? '-';
        $keperluan = $p->keperluan ?? '-';
        $petugas   = $p->processedBy->name ?? '-';

        // Hitung tinggi baris
        $lnNama  = $pdf->getNumLines($nama, $cols['Nama'] - 4);
        $lnJenis = $pdf->getNumLines($jenis, $cols['Jenis Surat'] - 4);
        $lnKep   = $pdf->getNumLines($keperluan, $cols['Keperluan'] - 4);
        $rowH    = max($lnNama, $lnJenis, $lnKep) * 4.2 + 3.5;
        $rowH    = max($rowH, 8.5);

        // Page break
        if ($pdf->GetY() + $rowH > $pdf->getPageHeight() - $pdf->getMargins()['bottom'] - 20) {
            $pdf->AddPage();
            $this->drawTableHeader($pdf, $cols, $lm);
        }

        $y = $pdf->GetY();
        $x = $lm;

        // Gambar bg baris sekaligus
        $pdf->SetFillColor(...$bg);
        $pdf->Rect($lm, $y, array_sum($cols), $rowH, 'F');

        // ── No ──
        $pdf->SetFont('helvetica', '', 6.8);
        $pdf->SetTextColor(...self::C_GRAY);
        $pdf->SetXY($x, $y + ($rowH - 4) / 2);
        $pdf->Cell($cols['No'], 4, (string)$no, 0, 0, 'C');
        $x += $cols['No'];

        // ── Kode ──
        $pdf->SetFont('courier', 'B', 6.8);
        $pdf->SetTextColor(...self::C_PRIMARY);
        $pdf->SetXY($x + 1, $y + ($rowH - 4.5) / 2);
        $pdf->Cell($cols['Kode'] - 2, 4.5, $p->kode_unik ?? '-', 0, 0, 'L');
        $x += $cols['Kode'];

        // ── Nama ──
        $pdf->SetFont('helvetica', 'B', 7.2);
        $pdf->SetTextColor(...self::C_DARK);
        $pdf->SetXY($x + 1.5, $y + 2);
        $pdf->MultiCell($cols['Nama'] - 3, 4.2, $nama, 0, 'L');
        $x += $cols['Nama'];

        // ── NIK ──
        $pdf->SetFont('courier', '', 6.5);
        $pdf->SetTextColor(...self::C_GRAY_D);
        $pdf->SetXY($x, $y + ($rowH - 4) / 2);
        $pdf->Cell($cols['NIK'], 4, $nik, 0, 0, 'C');
        $x += $cols['NIK'];

        // ── Jenis Surat ──
        $pdf->SetFont('helvetica', '', 7.2);
        $pdf->SetTextColor(...self::C_DARK);
        $pdf->SetXY($x + 1.5, $y + 2);
        $pdf->MultiCell($cols['Jenis Surat'] - 3, 4.2, $jenis, 0, 'L');
        $x += $cols['Jenis Surat'];

        // ── Keperluan ──
        $pdf->SetFont('helvetica', '', 6.8);
        $pdf->SetTextColor(...self::C_GRAY_D);
        $pdf->SetXY($x + 1.5, $y + 2);
        $pdf->MultiCell($cols['Keperluan'] - 3, 4, $keperluan, 0, 'L');
        $x += $cols['Keperluan'];

        // ── Status badge ──
        $bW = $cols['Status'] - 5;
        $bH = 5.5;
        $bX = $x + 2.5;
        $bY = $y + ($rowH - $bH) / 2;
        $pdf->SetFillColor(...$statusCfg['bg']);
        $pdf->SetDrawColor(...$statusCfg['color']);
        $pdf->SetLineWidth(0.25);
        $pdf->RoundedRect($bX, $bY, $bW, $bH, 1.5, '1111', 'DF');
        $pdf->SetFont('helvetica', 'B', 6.2);
        $pdf->SetTextColor(...$statusCfg['color']);
        $pdf->SetXY($bX, $bY);
        $pdf->Cell($bW, $bH, $statusCfg['label'], 0, 0, 'C');
        $x += $cols['Status'];

        // ── Tgl Masuk ──
        $pdf->SetFont('courier', '', 6.5);
        $pdf->SetTextColor(...self::C_GRAY_D);
        $pdf->SetXY($x, $y + ($rowH - 4) / 2);
        $pdf->Cell($cols['Tgl Masuk'], 4, optional($p->created_at)->format('d/m/Y H:i') ?? '-', 0, 0, 'C');
        $x += $cols['Tgl Masuk'];

        // ── Tgl Selesai ──
        $pdf->SetXY($x, $y + ($rowH - 4) / 2);
        $pdf->Cell($cols['Tgl Selesai'], 4, optional($p->processed_at)->format('d/m/Y H:i') ?? '-', 0, 0, 'C');
        $x += $cols['Tgl Selesai'];

        // ── Petugas ──
        $pdf->SetFont('helvetica', '', 6.8);
        $pdf->SetTextColor(...self::C_DARK);
        $pdf->SetXY($x + 1, $y + 2);
        $pdf->MultiCell($cols['Petugas'] - 2, 4, $petugas, 0, 'L');

        // Garis bawah
        $pdf->SetDrawColor(...self::C_BORDER_L);
        $pdf->SetLineWidth(0.15);
        $pdf->Line($lm, $y + $rowH, $lm + array_sum($cols), $y + $rowH);

        $pdf->SetY($y + $rowH);
    }

    // ─── 5. FOOTER ───────────────────────────────────────────────────────────
    private function drawFooter(TCPDF $pdf, string $namaInstansi, string $bulanLabel, string $kepalaDesaNama = '', string $kepalaDesaNip = ''): void
    {
        $lm    = 15;
        $w     = 267;
        $pageH = $pdf->getPageHeight();
        $bm    = $pdf->getMargins()['bottom'];

        if ($pdf->GetY() > $pageH - $bm - 52) {
            $pdf->AddPage();
        }

        $pdf->Ln(8);

        // Garis divider
        $pdf->SetDrawColor(...self::C_BORDER);
        $pdf->SetLineWidth(0.35);
        $pdf->Line($lm, $pdf->GetY(), $lm + $w, $pdf->GetY());
        $pdf->Ln(6);

        // Catatan legalitas kiri
        $noteY = $pdf->GetY();
        $pdf->SetFont('helvetica', 'I', 7);
        $pdf->SetTextColor(...self::C_GRAY);
        $pdf->SetX($lm);
        $pdf->MultiCell($w * 0.60, 4.5,
            "Laporan ini digenerate secara otomatis oleh Sistem Permohonan Surat.\n" .
            "Dokumen ini sah tanpa tanda tangan basah apabila dicetak langsung dari sistem resmi.",
            0, 'L'
        );

        // Blok TTD kanan
        $ttdW = $w * 0.28;
        $ttdX = $lm + $w - $ttdW;
        $ttdY = $noteY;

        $pdf->SetFont('helvetica', '', 8.5);
        $pdf->SetTextColor(...self::C_DARK);
        $pdf->SetXY($ttdX, $ttdY);
        $pdf->Cell($ttdW, 5.5, 'Mengetahui,', 0, 1, 'C');

        $pdf->SetFont('helvetica', 'I', 7.5);
        $pdf->SetTextColor(...self::C_GRAY_D);
        $pdf->SetXY($ttdX, $pdf->GetY());
        $pdf->Cell($ttdW, 5, 'Kepala ' . $namaInstansi, 0, 1, 'C');

        $pdf->Ln(14); // ruang TTD

        $lineX1 = $ttdX + $ttdW * 0.08;
        $lineX2 = $ttdX + $ttdW * 0.92;
        $pdf->SetDrawColor(...self::C_DARK);
        $pdf->SetLineWidth(0.5);
        $pdf->Line($lineX1, $pdf->GetY(), $lineX2, $pdf->GetY());

        // Nama kepala desa (dinamis atau placeholder)
        $namaLabel = $kepalaDesaNama ?: '(_____________________)';
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->SetTextColor(...self::C_DARK);
        $pdf->SetXY($ttdX, $pdf->GetY() + 2);
        $pdf->Cell($ttdW, 5, $namaLabel, 0, 1, 'C');

        // NIP (dinamis atau placeholder)
        $nipLabel = $kepalaDesaNip ? "NIP. $kepalaDesaNip" : 'NIP. ________________________________';
        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetTextColor(...self::C_GRAY);
        $pdf->SetXY($ttdX, $pdf->GetY());
        $pdf->Cell($ttdW, 4.5, $nipLabel, 0, 1, 'C');

        // ── Footer bar bawah ──
        $footerY = $pageH - $bm - 7;
        $pdf->SetFillColor(...self::C_KOP_TOP);
        $pdf->Rect($lm, $footerY, $w, 0.8, 'F');

        $pdf->SetFont('helvetica', '', 6.5);
        $pdf->SetTextColor(...self::C_GRAY);
        $pdf->SetXY($lm, $footerY + 2);
        $pdf->Cell($w, 4.5,
            'Halaman ' . $pdf->getAliasNumPage() . ' dari ' . $pdf->getAliasNbPages() .
            '   |   Sistem Permohonan Surat — ' . $namaInstansi .
            '   |   Dokumen rahasia, tidak untuk disebarluaskan',
            0, 0, 'C'
        );
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────
    private function panelHeader(TCPDF $pdf, string $title, float $x, float $y, float $w): void
    {
        // Accent strip kiri
        $pdf->SetFillColor(...self::C_PRIMARY);
        $pdf->RoundedRect($x, $y, 3, 7, 1, '1111', 'F');

        // BG label
        $pdf->SetFillColor(...self::C_PRIMARY_L);
        $pdf->RoundedRect($x + 3, $y, $w - 3, 7, 2, '0110', 'F');

        $pdf->SetFont('helvetica', 'B', 8.5);
        $pdf->SetTextColor(...self::C_PRIMARY);
        $pdf->SetXY($x + 7, $y + 0.8);
        $pdf->Cell($w - 9, 5.5, $title, 0, 1, 'L');
    }
}