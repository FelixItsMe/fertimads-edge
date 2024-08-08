<!DOCTYPE html>
<html>

<head>
  <title>Laporan Hama</title>
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
  <h2>Laporan Hama</h2>
  <table>
    <thead>
      <tr>
        <th>Waktu</th>
        <th>Nama Penyakit</th>
        <th>Nama Hama</th>
        <th>Kebun</th>
        <th>Komoditi</th>
        <th>Populasi Terinfeksi</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($reports as $pest)
      <tr>
        <td>{{ $pest->created_at->format('d M Y H:i:s') }}</td>
        <td>{{ $pest->disease_name }}</td>
        <td>{{ $pest->pest_name }}</td>
        <td>{{ $pest->garden->name }}</td>
        <td>{{ $pest->commodity->name }}</td>
        <td>{{ $pest->infected_count }}</td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="text-center">Tidak ada data</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</body>

</html>
