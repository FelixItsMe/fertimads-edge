<!DOCTYPE html>
<html>

<head>
  <title>Laporan Pemupukan</title>
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      border: 1px solid black;
      padding: 8px;
      text-align: left;
    }

    th {
      background-color: #f2f2f2;
    }
  </style>
</head>

<body>
  <h2>Laporan Pemupukan</h2>
  <table>
    <thead>
      <tr>
        <th>Tanggal</th>
        <th>Nama Lahan</th>
        <th>Nama Kebun</th>
        <th>Jenis Pupuk</th>
        <th>Volume Pupuk</th>
        <th>Total Waktu</th>
      </tr>
    </thead>
    <tbody>
      @foreach($reports as $report)
      <tr>
        <td>{{ $report->created_at->format('d M Y H:i:s') }}</td>
        <td>{{ $report->deviceSelenoid->garden->land->name }}</td>
        <td>{{ $report->deviceSelenoid->garden->name }}</td>
        <td>{{ $report->pemupukan_type }}</td>
        <td>{{ number_format($report->total_volume, 2) }} Ltr</td>
        <td>{{ $report->time_in_hours }} Jam</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</body>

</html>
