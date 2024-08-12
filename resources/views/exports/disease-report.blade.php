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
  <h2>Laporan Penyakit</h2>
  <table>
    <thead>
      <tr>
        <th>Waktu</th>
        <th>Nama Penyakit</th>
        <th>Kategori</th>
        <th>Pestisida</th>
        <th>Kategori Kerja</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($reports as $disease)
      <tr>
        <td>{{ $disease->created_at->format('d M Y H:i:s') }}</td>
        <td>{{ $disease->name }}</td>
        <td>{{ $disease->category }}</td>
        <td>{{ $disease->pestisida }}</td>
        <td>{{ $disease->works_category }}</td>
      </tr>
      @empty
      <tr>
        <td colspan="5" class="text-center">Tidak ada data</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</body>

</html>
