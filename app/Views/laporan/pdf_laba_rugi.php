<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi</title>
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

        .border-top {
            border-top: 1px solid #000;
        }

        .border-bottom {
            border-bottom: 1px solid #000;
        }

        .font-weight-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="text-center">
        <h2>Laporan Laba Rugi</h2>
        <h4>Periode: <?= date('d M Y', strtotime($startDate)) ?> s/d <?= date('d M Y', strtotime($endDate)) ?></h4>
    </div>
    <br>
    <table>
        <tbody>
            <tr>
                <td colspan="2" class="font-weight-bold">Pendapatan</td>
            </tr>
            <?php foreach ($pendapatanDetails as $item) : ?>
                <tr>
                    <td style="padding-left: 30px;"><?= esc($item['nama_akun']) ?></td>
                    <td class="text-right"><?= number_format($item['balance'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td class="font-weight-bold" style="padding-left: 30px;">Total Pendapatan</td>
                <td class="text-right border-top font-weight-bold"><?= number_format($totalPendapatan, 2, ',', '.') ?></td>
            </tr>

            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>

            <tr>
                <td colspan="2" class="font-weight-bold">Beban Operasional</td>
            </tr>
            <?php foreach ($bebanDetails as $item) : ?>
                <tr>
                    <td style="padding-left: 30px;"><?= esc($item['nama_akun']) ?></td>
                    <td class="text-right"><?= number_format($item['balance'], 2, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td class="font-weight-bold" style="padding-left: 30px;">Total Beban</td>
                <td class="text-right border-top">(<?= number_format($totalBeban, 2, ',', '.') ?>)</td>
            </tr>

            <tr>
                <td colspan="2">&nbsp;</td>
            </tr>

            <tr>
                <th class="border-top border-bottom">LABA / (RUGI) BERSIH</th>
                <th class="text-right border-top border-bottom">
                    <?= number_format($labaRugi, 2, ',', '.') ?>
                </th>
            </tr>
        </tbody>
    </table>
</body>

</html>