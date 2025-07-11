<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Laporan Buku Besar</title>
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
            border: 1px solid #000;
            padding: 5px;
        }

        th {
            background-color: #f2f2f2;
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
    </style>
</head>

<body>
    <div class="text-center">
        <h2>Laporan Buku Besar</h2>
        <h4>Akun: <?= esc($selectedAccount['kode_akun']) ?> - <?= esc($selectedAccount['nama_akun']) ?></h4>
        <p>Periode: Hingga <?= date('d F Y', strtotime($endDate)) ?></p>
    </div>
    <br>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Kredit</th>
                <th class="text-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // PERBAIKAN DI SINI: Saldo tidak lagi dimulai dari 0
            $saldo = $selectedAccount['posisi_saldo'] == 'debit' ? $selectedAccount['saldo_awal'] : ($selectedAccount['saldo_awal'] * -1);
            ?>
            <tr>
                <td colspan="4" style="font-weight: bold;">Saldo Awal</td>
                <td class="text-right" style="font-weight: bold;">
                    <?= number_format($selectedAccount['posisi_saldo'] == 'debit' ? $saldo : $saldo * -1, 2, ',', '.') ?>
                </td>
            </tr>

            <?php foreach ($reportData as $row) : ?>
                <?php
                // Logika perhitungan saldo berjalan sekarang sudah benar karena dimulai dari saldo awal
                $saldo += $row['debit'] - $row['kredit'];
                ?>
                <tr>
                    <td><?= date('d-m-Y', strtotime($row['tanggal_jurnal'])) ?></td>
                    <td><?= esc($row['deskripsi']) ?></td>
                    <td class="text-right"><?= number_format($row['debit'], 2, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($row['kredit'], 2, ',', '.') ?></td>
                    <td class="text-right">
                        <?= number_format($selectedAccount['posisi_saldo'] == 'debit' ? $saldo : ($saldo * -1), 2, ',', '.') ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-center">Saldo Akhir</th>
                <th class="text-right">
                    <?= number_format($selectedAccount['posisi_saldo'] == 'debit' ? $saldo : ($saldo * -1), 2, ',', '.') ?>
                </th>
            </tr>
        </tfoot>
    </table>
</body>

</html>