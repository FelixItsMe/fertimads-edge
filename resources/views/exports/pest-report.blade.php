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
        <th>Penyebab</th>
        <th>Pengedalian</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($reports as $pest)
      <?php
      $response = json_decode($pest->gemini_response);
      ?>
      <tr>
        <td>{{ $pest->created_at->format('d M Y H:i:s') }}</td>
        <td>{{ $pest->disease_name }}</td>
        <td>{{ $pest->pest_name }}</td>
        <td>{{ $pest->garden->name }}</td>
        <td>
          @if (!is_null($response))
          @if (gettype($response->penyebab) === 'array')
          <ul class="gemini-list">
            @foreach ($response->penyebab as $penyebab)
            <li class="gemini-list-item">{!! $penyebab !!}</li>
            @endforeach
          </ul>
          @else
          <p>{!! $response->penyebab !!}</p>
          @endif
          @else
          <p>-</p>
          @endif
        </td>
        <td>
          @if (!is_null($response))
          @if (gettype($response->pengendalian) === 'array')
          <ul class="gemini-list">
            @foreach ($response->pengendalian as $pengendalian)
            <li class="gemini-list-item">{!! $pengendalian !!}</li>
            @endforeach
          </ul>
          @else
          <p>{!! $response->pengendalian !!}</p>
          @endif
          @else
          <p>-</p>
          @endif
        </td>
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
