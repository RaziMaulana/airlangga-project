<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 15px; }
        .header h3 { margin: 0; }
        .header p { margin: 3px 0; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #aaa; padding: 4px 2px; text-align: center; }
        th { background-color: #eee; }
        .text-left { text-align: left; padding-left: 5px; }
        .bg-hadir { background-color: #d1fae5; color: #065f46; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h3>{{ $title }}</h3>
        <p>{{ $subtitle }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Nama Siswa</th>
                @for($d = 1; $d <= $daysInMonth; $d++)
                    <th style="width: 2.2%;">{{ $d }}</th>
                @endfor
                <th style="width: 5%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapSiswa as $uid => $data)
                <tr>
                    <td class="text-left">{{ $data['nama'] }}</td>
                    @for($d = 1; $d <= $daysInMonth; $d++)
                        <td class="{{ $data['presensi'][$d]['in'] ? 'bg-hadir' : '' }}">
                            {{ $data['presensi'][$d]['in'] ? 'H' : '-' }}
                        </td>
                    @endfor
                    <td><strong>{{ $data['total_in'] }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
