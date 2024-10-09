<!DOCTYPE html>
<html>

<head>
  <title>Laporan Penyakit</title>
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
        <th>Jenis Pestisida</th>
        <th>Cara Kerja</th>
        <th>Golongan Senyawa Kimia</th>
        <th>Bahan Aktif</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($reports as $disease)
      <tr>
        <td>{{ $disease->created_at->format('d M Y H:i:s') }}</td>
        <td>{{ $disease->name }}</td>
        <td>{{ $disease->category }}</td>
        <td>
          @if (is_array(json_decode($disease->pestisida)))
          <ul class="gemini-list">
            @foreach (json_decode($disease->pestisida) as $pestisida)
            <li class="gemini-list-item">{!! $pestisida !!}</li>
            @endforeach
          </ul>
          @else
          <p>{!! $disease->pestisida !!}</p>
          @endif
        </td>
        <td>
          @if (is_array(json_decode($disease->works_category)))
          <ul class="gemini-list">
            @forelse (json_decode($disease->works_category) as $works_category)
            <li class="gemini-list-item">{!! $works_category !!}</li>
            @empty
            <p>-</p>
            @endforelse
          </ul>
          @else
          <p>{!! $disease->works_category !!}</p>
          @endif
        </td>
        <td>
          @if (is_array(json_decode($disease->chemical)))
          <ul class="gemini-list">
            @forelse (json_decode($disease->chemical) as $chemical)
            <li class="gemini-list-item">{!! $chemical !!}</li>
            @empty
            <p>-</p>
            @endforelse
          </ul>
          @else
          <p>{!! $disease->chemical !!}</p>
          @endif
        </td>
        <td>
          @if (is_array(json_decode($disease->active_materials)))
          <ul class="gemini-list">
            @forelse (json_decode($disease->active_materials) as $active_material)
            <li class="gemini-list-item">{!! $active_material !!}</li>
            @empty
            <p>-</p>
            @endforelse
          </ul>
          @else
          <p>{!! $disease->active_materials !!}</p>
          @endif
        </td>
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
