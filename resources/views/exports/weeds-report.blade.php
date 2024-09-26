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
  <h2>Laporan Gulma</h2>
  <table>
    <thead>
      <tr>
        <th>Waktu</th>
        <th>Nama Gulma</th>
        <th>Cara Kerja</th>
        <th>Golongan Senyawa Kimia</th>
        <th>Bahan Aktif</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($reports as $weed)
      <tr>
        <td>{{ $weed->created_at->format('d M Y H:i:s') }}</td>
        <td>{{ $weed->nama_gulma }}</td>
        <td>{{ $weed->klasifikasi_berdasarkan_cara_kerja }}</td>
        <td>{!! $weed->golongan_senyawa_kimia !!}</td>
        <td>{!! $weed->bahan_aktif !!}</td>
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
