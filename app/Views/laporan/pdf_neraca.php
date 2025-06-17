<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Laporan Neraca</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 5px;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        h2,
        h4 {
            margin: 0;
        }

        .font-weight-bold {
            font-weight: bold;
        }

        .border-top {
            border-top: 1px solid #000;
        }

        .border-bottom {
            border-bottom: 2px double #000;
        }
    </style>
</head>

<body>
    <div class="text-center">
        <h2>Laporan Posisi Keuangan (Neraca)</h2>
        <h4>Posisi per Tanggal: <?= date('d F Y', strtotime($endDate)) ?></h4>
    </div>
    <br>
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; padding-right: 10px;">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="text-align: left;">ASET</th>
                            <th class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($asetDetails as $item): ?>
                            <tr>
                                <td><?= esc($item['nama_akun']) ?></td>
                                <td class="text-right"><?= number_format($item['balance'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th style="text-align: left;">TOTAL ASET</th>
                            <th class="text-right border-top border-bottom">Rp <?= number_format($totalAset, 2, ',', '.') ?></th>
                        </tr>
                    </tfoot>
                </table>
            </td>
            <td style="width: 50%; padding-left: 10px;">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="text-align: left;">LIABILITAS DAN EKUITAS</th>
                            <th class="text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2" class="font-weight-bold">Liabilitas</td>
                        </tr>
                        <?php foreach ($liabilitasDetails as $item): ?>
                            <tr>
                                <td style="padding-left: 20px;"><?= esc($item['nama_akun']) ?></td>
                                <td class="text-right"><?= number_format($item['balance'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <th style="padding-left: 20px;">Total Liabilitas</th>
                            <th class="text-right border-top"><?= number_format($totalLiabilitas, 2, ',', '.') ?></th>
                        </tr>

                        <tr>
                            <td colspan="2" class="font-weight-bold">Ekuitas</td>
                        </tr>
                        <?php foreach ($ekuitasDetails as $item): ?>
                            <tr>
                                <td style="padding-left: 20px;"><?= esc($item['nama_akun']) ?></td>
                                <td class="text-right"><?= number_format($item['balance'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td style="padding-left: 20px;">Laba (Rugi) Periode Berjalan</td>
                            <td class="text-right"><?= number_format($labaRugiBerjalan, 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <th style="padding-left: 20px;">Total Ekuitas</th>
                            <th class="text-right border-top"><?= number_format($totalEkuitas, 2, ',', '.') ?></th>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th style="text-align: left;">TOTAL LIABILITAS DAN EKUITAS</th>
                            <th class="text-right border-top border-bottom">Rp <?= number_format($totalLiabilitas + $totalEkuitas, 2, ',', '.') ?></th>
                        </tr>
                    </tfoot>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>