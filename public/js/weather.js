const dayNames = [
    "Minggu",
    "Senin",
    "Selasa",
    "Rabu",
    "Kamis",
    "Jum\'at",
    "Sabtu"
]

const bmkgWether = async ({ eTemp, eHumid, eWindSpeed, eMaxT, eMinT, eWeatherName, eWeatherIcon, eTime, eDay }) => {
    const res = await fetch(
        "https://data.bmkg.go.id/DataMKG/MEWS/DigitalForecast/DigitalForecast-JawaBarat.xml", {
            method: "GET",
        }
    );

    if (!res.ok) {
      return false
    }

    const now = new Date()

    const str = await res.text()
    const data = await new window.DOMParser().parseFromString(str, "text/xml")

    const currentHours = Math.round(now.getHours() / 6) * 6
    const parameters = data.getElementsByTagName('area')[16].getElementsByTagName('parameter')
    const humidities = parameters.hu
    const temperatures = parameters.t
    const maxTs = parameters.tmax
    const minTs = parameters.tmin
    const windSpeeds = parameters.ws
    const weathers = parameters.weather

    eTime.textContent = now.getHours() + ":" + now.getMinutes()
    eDay.textContent = dayNames[now.getDay()]

    for (const humidity of humidities.children) {
      if (humidity.attributes.h.nodeValue == currentHours) {
        eHumid.textContent = humidity.children[0].textContent
      }
    }
    for (const temperature of temperatures.children) {
      if (temperature.attributes.h.nodeValue == currentHours) {
        eTemp.textContent = temperature.children[0].textContent
      }
    }

    eMaxT.textContent = maxTs.children[0].children[0].textContent
    eMinT.textContent = minTs.children[0].children[0].textContent

    for (const windSpeed of windSpeeds.children) {
      if (windSpeed.attributes.h.nodeValue == currentHours) {
        eWindSpeed.textContent = windSpeed.children[2].textContent
      }
    }
    for (const weather of weathers.children) {
      if (weather.attributes.h.nodeValue == currentHours) {
        const [weatherName, weatherIcon] = weatherNames(parseInt(weather.children[0].textContent))
        eWeatherName.textContent = weatherName
        eWeatherIcon.innerHTML = `<i class="fa-solid fa-${weatherIcon}"></i>`
      }
    }
}

const weatherNames = code => {
    switch (code) {
        case 0:
            return ["Cerah", "sun"]
            break;
        case 1:
            return ["Cerah Berawan", "cloud-sun"]
            break;
        case 2:
            return ["Cerah Berawan", "cloud-sun"]
            break;
        case 3:
            return ["Berawan", "cloud"]
            break;
        case 4:
            return ["Berawan Tebal", "cloud"]
            break;
        case 5:
            return ["Udara Kabur", "smog"]
            break;
        case 10:
            return ["Asap", "smog"]
            break;
        case 45:
            return ["Kabut", "smog"]
            break;
        case 60:
            return ["Hujan Ringan", "cloud-rain"]
            break;
        case 61:
            return ["Hujan Sedang", "cloud-showers-heavy"]
            break;
        case 63:
            return ["Hujan Lebat", "cloud-showers-heavy"]
            break;
        case 80:
            return ["Hujan Lokal", "cloud-showers-heavy"]
            break;
        case 95:
            return ["Hujan Petir", "cloud-bolt"]
            break;
        case 97:
            return ["Hujan Petir", "cloud-bolt"]
            break;

        default:
            return ["", ""]
            break;
    }
}

const weatherHtml = () => {
    return `<div class="inline-block overflow-hidden text-left align-bottom transition-all transform bg-gradient-to-br from-blue-600 to-blue-900 rounded-lg shadow-xl sm:align-middle sm:max-w-2xl sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
        <div class="p-3 flex flex-row gap-2 text-white">
            <div>
            <div>
                <div class="text-xs md:text-lg font-extrabold lato-regular" id="bmkg-day">Jumat</div>
                <div class="text-xs md:text-6xl lato-regular relative"><span class="font-extrabold" id="bmkg-temp">26</span><span class="absolute md:-top-4">°</span></div>
            </div>
            <div class="text-xs font-semibold text-slate-50/50">Last Updated <span id="bmkg-times">11:50</span></div>
            <div><i class="fa-solid fa-location-dot"></i>&nbsp;<span class="text-xs">Kota Bogor</span></div>
            </div>
            <div class="grid grid-cols-1 content-between">
            <div>
                <div class="text-xs md:text-base"><i class="fa-solid fa-wind"></i>&nbsp;<span id="bmkg-ws">28</span> km/h</div>
                <div class="text-xs md:text-base"><i class="fa-solid fa-droplet"></i>&nbsp;<span id="bmkg-humid">42</span>%</div>
            </div>
            <div>
                <div>H&nbsp;<span id="bmkg-max-t">30</span>°C</div>
                <div>L&nbsp;<span id="bmkg-min-t">20</span>°C</div>
            </div>
            </div>
            <div class="text-center">
            <div class="text-4xl md:text-8xl" id="bmkg-weather-icon"><i class="fa-solid fa-moon"></i></div>
            <div class="text-lg text-slate-50/50" id="bmkg-weather-name">Clear</div>
            </div>
        </div>
    </div>`;
}
