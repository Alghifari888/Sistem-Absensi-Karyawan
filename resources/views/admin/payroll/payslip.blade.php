<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $user->name }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; }
        .container { width: 100%; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 0; }
        .content table { width: 100%; border-collapse: collapse; }
        .content th, .content td { border: 1px solid #ddd; padding: 8px; }
        .content th { background-color: #f2f2f2; text-align: left; }
        .text-right { text-align: right; }
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>SLIP GAJI</h1>
            <p>Periode: {{ $bulan }} {{ $tahun }}</p>
        </div>

        <div class="content">
            <table>
                <tr>
                    <th>Nama Karyawan</th>
                    <td>{{ $user->name }}</td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td>{{ ucfirst($user->role) }}</td>
                </tr>
            </table>

            <br>

            <h4>Rincian Pendapatan</h4>
            <table>
                <thead>
                    <tr>
                        <th>Keterangan</th>
                        <th class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Gaji Pokok</td>
                        <td class="text-right">Rp {{ number_format($gaji_pokok, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Upah Lembur ({{ $total_jam_lembur }} Jam)</td>
                        <td class="text-right">Rp {{ number_format($upah_lembur, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Total Pendapatan (Take Home Pay)</th>
                        <th class="text-right">Rp {{ number_format($gaji_total, 2, ',', '.') }}</th>
                    </tr>
                </tbody>
            </table>

            <br>
            
            <h4>Rincian Kehadiran</h4>
            <table>
                <tr>
                    <th>Total Hari Kehadiran</th>
                    <td class="text-right">{{ $total_kehadiran }} Hari</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <p>Ini adalah dokumen yang dibuat secara otomatis oleh sistem.</p>
        </div>
    </div>
</body>
</html>