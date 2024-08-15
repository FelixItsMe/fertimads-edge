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
            @php
                $telemetry = (array) $deviceTelemetry->telemetry;
            @endphp
            @for ($i = 1; $i <= 4; $i++)
                <tr>
                    <td>{{ $deviceTelemetry->created_at }}</td>
                    <td>Selenoid {{ $i }}</td>
                    <td>{{ $telemetry['SS' . $i]->N }}&nbsp;mg/kg</td>
                    <td>{{ $telemetry['SS' . $i]->P }}&nbsp;mg/kg</td>
                    <td>{{ $telemetry['SS' . $i]->K }}&nbsp;mg/kg</td>
                    <td>{{ $telemetry['SS' . $i]->EC }}&nbsp;uS/cm</td>
                    <td>{{ $telemetry['SS' . $i]->pH }}</td>
                    <td>{{ $telemetry['SS' . $i]->T }}°C</td>
                    <td>{{ $telemetry['SS' . $i]->H }}%</td>
                    <td>{{ number_format($telemetry['DHT1']->T, 2) }}°C</td>
                    <td>{{ number_format($telemetry['DHT1']->H, 2) }}%</td>
                </tr>
            @endfor
        @endforeach
    </tbody>
</table>
