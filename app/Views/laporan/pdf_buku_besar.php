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
            $saldo = 0;
            ?>
            <?php foreach ($reportData as $row) : ?>
                <?php
                if ($selectedAccount['posisi_saldo'] == 'debit') {
                    $saldo += $row['debit'] - $row['kredit'];
                } else {
                    $saldo += $row['kredit'] - $row['debit'];
                }
                ?>
                <tr>
                    <td><?= date('d-m-Y', strtotime($row['tanggal_jurnal'])) ?></td>
                    <td><?= esc($row['deskripsi']) ?></td>
                    <td class="text-right"><?= number_format($row['debit'], 2, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($row['kredit'], 2, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($saldo, 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-center">Saldo Akhir</th>
                <th class="text-right"><?= number_format($saldo, 2, ',', '.') ?></th>
            </tr>
        </tfoot>
    </table>
</body>

</html>