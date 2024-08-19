<table>
    <thead>
        <tr>
            <th>Waktu</th>
            <th>Selenoid</th>
            <th>Nitrogen</th>
            <th>Fosfor</th>
            <th>Kalium</th>
            <th>EC</th>
            <th>pH Tanah</th>
            <th>Suhu Tanah</th>
            <th>Kelembapan Tanah</th>
            <th>Suhu Lingkungan</th>
            <th>Kelembapan Lingkungan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($deviceTelemetries as $deviceTelemetry)
        <tr>
            <td>{{ $deviceTelemetry->created_at }}</td>
            <td>Selenoid {{ $deviceTelemetry->selenoid }}</td>
            <td>{{ $deviceTelemetry->N }}</td>
            <td>{{ $deviceTelemetry->P }}</td>
            <td>{{ $deviceTelemetry->K }}</td>
            <td>{{ $deviceTelemetry->EC }}</td>
            <td>{{ $deviceTelemetry->pH }}</td>
            <td>{{ $deviceTelemetry->T }}</td>
            <td>{{ $deviceTelemetry->H }}</td>
            <td>{{ $deviceTelemetry->dhtT }}</td>
            <td>{{ $deviceTelemetry->dhtH }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
