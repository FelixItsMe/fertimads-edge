<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Jadwal Kegiatan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-4">
            @if (session()->has('infrastructure-success'))
                <div class="bg-sky-500 text-white w-full p-6 sm:rounded-lg flex items-center">
                    <i
                        class="fa-solid fa-circle-info text-3xl mr-3"></i>&nbsp;{{ session()->get('infrastructure-success') }}
                </div>
            @endif
            <div class="grid grid-flow-row grid-cols-1 lg:grid-cols-2 gap-8">
                <div>
                    <div class="bg-white p-4">
                        <input type="hidden" name="execute_date">
                        <div class="py-2 px-4 bg-primary text-white flex flex-row justify-between">
                            <div id="month-year-text" class="text-2xl font-extrabold"></div>
                            <div class="flex gap-2">
                                <button type="button" class="hover:text-slate-400" onclick="subMonth()"><i
                                        class="fa-solid fa-chevron-left"></i></button>
                                <button type="button" class="hover:text-slate-400" onclick="addMonth()"><i
                                        class="fa-solid fa-chevron-right"></i></button>
                            </div>
                        </div>
                        <div id="calendar"></div>
                    </div>
                </div>
                <div>
                    <div class="flex flex-col gap-2">
                        <h3 class="text-xl font-bold">Pilih Kebun</h3>
                        <div class="flex flex-wrap space-x-2" id="list-garden">
                        </div>
                        <div id="list-detail-schedule" class="flex flex-col space-y-2">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="{{ asset('js/api.js') }}"></script>
        <script>
            // Get current date
            let today = new Date();
            let currentDate = today.getDate();
            let currentMonth = today.getMonth();
            let currentYear = today.getFullYear();
            let currentFullDate =
                `${currentYear}-${(currentMonth + 1).toString().padStart(2, "0")}-${currentDate.toString().padStart(2, "0")}`;
            const calendarEl = document.getElementById('calendar');
            let controller
            let controllerDetailGardenSchedules
            let pickedDate = currentFullDate

            const eListDetailSchedules = document.querySelector('#list-detail-schedule')

            const getSchedules = async (year, month) => {
                if (controller) {
                    controller.abort()
                }

                controller = new AbortController();
                const signal = controller.signal;
                const data = await fetchData(
                    "{{ route('activity-schedule.schedule-in-month', ['month' => 'MONTH', 'year' => 'YEAR']) }}"
                    .replace('MONTH', month)
                    .replace('YEAR', year), {
                        signal,
                        method: "GET",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
                                .nodeValue,
                            'Accept': 'application/json',
                        },
                    }
                );

                return data
            }

            const detailGardenSchedules = async (date, gardenId) => {
                if (controllerDetailGardenSchedules) {
                    controllerDetailGardenSchedules.abort()
                }

                controllerDetailGardenSchedules = new AbortController();
                const signal = controllerDetailGardenSchedules.signal;
                const data = await fetchData(
                    "{{ route('activity-schedule.detail', ['date' => 'DATE', 'garden' => 'GARDEN']) }}"
                    .replace('DATE', date)
                    .replace('GARDEN', gardenId), {
                        signal,
                        method: "GET",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
                                .nodeValue,
                            'Accept': 'application/json',
                        },
                    }
                );

                return data
            }

            const getDetailSchedule = async date => {
                const eListGarden = document.querySelector('#list-garden')
                eListGarden.innerHTML = `<div class="bg-white px-4 py-2 rounded-md shadow-md flex justify-center">
                            <x-loading />
                          </div>`

                const data = await fetchData(
                    "{{ route('activity-schedule.date', ['date' => 'DATE']) }}"
                    .replace('DATE', date), {
                        method: "GET",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
                                .nodeValue,
                            'Accept': 'application/json',
                        },
                    }
                );

                eListGarden.innerHTML = ``
                eListDetailSchedules.innerHTML = ``

                if (!data) {
                    return 0
                }

                data.gardens.forEach(garden => {
                    const divSchedule = document.createElement('div')
                    divSchedule.classList.add('bg-white', 'px-4', 'py-2', 'rounded-md', 'shadow-md',
                        'cursor-pointer')
                    divSchedule.textContent = `${garden.name}`

                    divSchedule.onclick = e => {
                        const classes = ['bg-primary', 'text-white', 'active']

                        document.querySelector('#list-garden div.active')?.classList.remove(...classes)

                        e.target.classList.add(...classes)

                        scheduleGardenDetail(pickedDate, garden.id)
                    }

                    eListGarden.appendChild(divSchedule)
                });

                return data
            }

            const scheduleGardenDetail = async (date, gardenId) => {
                eListDetailSchedules.innerHTML = `<div class="bg-white p-4 rounded-md shadow-md flex justify-center">
                            <x-loading />
                          </div>`

                if (controllerDetailGardenSchedules) {
                    controllerDetailGardenSchedules.abort()
                }

                controllerDetailGardenSchedules = new AbortController();
                const signal = controllerDetailGardenSchedules.signal;
                const data = await fetchData(
                    "{{ route('activity-schedule.detail', ['date' => 'DATE', 'garden' => 'GARDEN']) }}"
                    .replace('DATE', date)
                    .replace('GARDEN', gardenId), {
                        signal,
                        method: "GET",
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').attributes.content
                                .nodeValue,
                            'Accept': 'application/json',
                        },
                    }
                );

                eListDetailSchedules.innerHTML = ``

                if (!data) {
                    return false
                }

                let eDetail = ``

                data.waterSchedules.forEach(waterSchedule => {
                  eDetail += initWaterSchedules(waterSchedule)
                });

                data.fertilizerSchedules.forEach(fertilizerSchedule => {
                  eDetail += initFertilizerSchedules(fertilizerSchedule)
                });

                eListDetailSchedules.innerHTML = eDetail
            }

            const initWaterSchedules = waterSchedule => {
                const date1 = parseDatetime(waterSchedule.start_time);
                const date2 = parseDatetime(waterSchedule.end_time);

                const diffInMiliseconds = date2 - date1

                const diffInMinutes = diffInMiliseconds / (1000 * 60)
                let actualDiffInMinutes = null

                let eActual = ``

                if (waterSchedule.device_schedule_execute) {
                    const actualDate1 = parseDatetime(waterSchedule.device_schedule_execute.start_time);
                    const actualDate2 = parseDatetime(waterSchedule.device_schedule_execute.end_time);

                    actualDiffInMinutes = (actualDate2 - actualDate1) / (1000 * 60)

                    eActual = `<div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xl font-bold text-slate-400">Aktual Volume</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xl text-slate-400" id="text-water-times">${waterSchedule.device_schedule_execute?.total_volume.toFixed(2)} Liter</span>
                                </div>
                            </div>
                            <div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xl font-bold text-slate-400">Aktual Durasi</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xl text-slate-400" id="text-water-times">${actualDiffInMinutes.toFixed(2)} Menit</span>
                                </div>
                            </div>`
                }

                return `<div class="bg-white p-4 rounded-md shadow-md">
                            <h3 class="text-3xl font-bold mb-4">Detail Informasi Jadwal Penyiraman</h3>
                            <div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xl font-bold text-slate-400">Volume</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xl text-slate-400" id="text-water-times">${waterSchedule.total_volume.toFixed(2)} Liter</span>
                                </div>
                            </div>
                            <div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xl font-bold text-slate-400">Durasi</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xl text-slate-400" id="text-water-times">${diffInMinutes.toFixed(2)} Menit</span>
                                </div>
                            </div>
                            <div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xl font-bold text-slate-400">Waktu</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xl text-slate-400" id="text-water-times">${waterSchedule.start_time}</span>
                                </div>
                            </div>
                            ${eActual}
                          </div>`
            }

            const initFertilizerSchedules = fertilizerSchedules => {
                const date1 = parseDatetime(fertilizerSchedules.execute_start);
                const date2 = parseDatetime(fertilizerSchedules.execute_end);

                const diffInMiliseconds = date2 - date1

                const diffInMinutes = diffInMiliseconds / (1000 * 60)
                let actualDiffInMinutes = null

                let eActual = ``

                if (fertilizerSchedules.schedule_execute) {
                    const actualDate1 = parseDatetime(fertilizerSchedules.schedule_execute.execute_start);
                    const actualDate2 = parseDatetime(fertilizerSchedules.schedule_execute.execute_end);

                    actualDiffInMinutes = (actualDate2 - actualDate1) / (1000 * 60)

                    eActual = `<div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xl font-bold text-slate-400">Aktual Volume</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xl text-slate-400" id="text-water-times">${fertilizerSchedules.schedule_execute?.total_volume.toFixed(2)} Liter</span>
                                </div>
                            </div>
                            <div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xl font-bold text-slate-400">Aktual Durasi</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xl text-slate-400" id="text-water-times">${actualDiffInMinutes.toFixed(2)} Menit</span>
                                </div>
                            </div>`
                }

                return `<div class="bg-white p-4 rounded-md shadow-md">
                            <h3 class="text-3xl font-bold mb-4">Detail Informasi Jadwal Pemupukan</h3>
                            <div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xl font-bold text-slate-400">Volume</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xl text-slate-400" id="text-water-times">${fertilizerSchedules.total_volume.toFixed(2)} Liter</span>
                                </div>
                            </div>
                            <div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xl font-bold text-slate-400">Durasi</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xl text-slate-400" id="text-water-times">${diffInMinutes.toFixed(2)} Menit</span>
                                </div>
                            </div>
                            <div class="grid grid-flow-row grid-cols-5 align-text-bottom">
                                <div class="col-span-2">
                                    <span class="text-xl font-bold text-slate-400">Waktu</span>
                                </div>
                                <div class="col-span-3">
                                    <span class="col-span-3 text-xl text-slate-400" id="text-water-times">${fertilizerSchedules.execute_start}</span>
                                </div>
                            </div>
                            ${eActual}
                          </div>`
            }

            function parseDatetime(datetimeStr) {
                // Split the datetime string into date and time components
                let [date, time] = datetimeStr.split(' ');

                // Split the date component into year, month, and day
                let [year, month, day] = date.split('-');

                // Split the time component into hours, minutes, and seconds
                let [hours, minutes, seconds] = time.split(':');

                // Create a new Date object (Note: months are 0-indexed in JavaScript Date)
                return new Date(year, month - 1, day, hours, minutes, seconds);
            }

            const generateCalendar = (month, year, availableSchedules) => {
                // Get the first day of the month
                const firstDay = new Date(year, month, 1);

                // Get the number of days in the month
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                // Get the day of the week for the first day
                const dayOfWeek = firstDay.getDay();

                // Get month name
                const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ];
                const currentMonthName = monthNames[month];

                document.querySelector('#month-year-text').textContent = `${currentMonthName} ${year}`

                // Create the table element
                const table = document.createElement('table');
                table.classList.add('table-auto', 'w-full');

                // Create the table header row
                const headerRow = document.createElement('tr');
                ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum\'at', 'Sabtu'].forEach(day => {
                    const headerCell = document.createElement('th');
                    headerCell.classList.add('text-center', 'text-xs', 'py-2', 'bg-gray-200', 'w-1/7');
                    headerCell.textContent = day;
                    headerRow.appendChild(headerCell);
                });
                table.appendChild(headerRow);

                // Create the calendar grid
                let currentDay = 1 - dayOfWeek; // Adjust for starting day
                while (currentDay <= daysInMonth) {
                    const row = document.createElement('tr');
                    for (let i = 0; i < 7; i++) {
                        const cell = document.createElement('td');
                        cell.classList.add('py-4', 'border', 'text-center', 'relative', 'cursor-pointer',
                            'hover:bg-primary',
                            'hover:text-white');
                        if (currentDay <= 0 || currentDay > daysInMonth) {
                            cell.classList.add('text-gray-400'); // Grey out days from previous/next month
                        } else {
                            const info = document.createElement('div');
                            const wBar = document.createElement('div');
                            const fBar = document.createElement('div');
                            const formatDate =
                                `${year}-${(month + 1).toString().padStart(2, "0")}-${currentDay.toString().padStart(2, "0")}`
                            cell.textContent = currentDay;
                            cell.setAttribute('data-date', formatDate);

                            if (formatDate == pickedDate) {
                                selectDate(cell)
                            }

                            cell.onclick = (e) => {
                                selectDate(e.target)
                            }

                            if (currentDay == new Date().getDate()) {
                                cell.classList.add('text-blue-500')
                            }

                            const schedule = availableSchedules.find(schedule => {
                                return schedule.date == formatDate
                            })

                            if (schedule.schedule.includes(1)) {
                                wBar.classList.add('bg-primary', 'w-full', 'h-1');
                            }
                            if (schedule.schedule.includes(2)) {
                                fBar.classList.add('bg-yellow-300', 'w-full', 'h-1');
                            }
                            info.classList.add(
                                'absolute',
                                'bottom-1',
                                'left-0',
                                'h-3',
                                'right-0',
                                'flex',
                                'flex-col',
                                'px-2'
                            );

                            info.appendChild(wBar)
                            info.appendChild(fBar)
                            cell.appendChild(info)
                        }
                        row.appendChild(cell);
                        currentDay++;
                    }
                    table.appendChild(row);
                }

                calendarEl.innerHTML = ""; // Clear previous calendar
                calendarEl.appendChild(table);
            }

            const selectDate = async e => {
                console.dir(e.dataset);
                if (!e.dataset.date) {
                    return false
                }
                const classes = ['bg-primary', 'text-white', 'active']

                document.querySelector('#calendar td.active')?.classList.remove(...classes)

                e.classList.add(...classes)

                pickedDate = e.dataset.date

                await getDetailSchedule(pickedDate)
            }

            const addMonth = async () => {
                currentMonth++
                if (currentMonth > 11) {
                    currentMonth = 0
                    currentYear++
                }

                const data = await getSchedules(currentYear, currentMonth + 1)

                generateCalendar(currentMonth, currentYear, data.schedules);
            }

            const subMonth = async () => {
                currentMonth--
                if (currentMonth < 0) {
                    currentMonth = 11
                    currentYear--
                }

                const data = await getSchedules(currentYear, currentMonth + 1)

                generateCalendar(currentMonth, currentYear, data.schedules);
            }

            window.onload = async () => {
                console.log('Hello World');

                const data = await getSchedules(currentYear, currentMonth + 1)

                // Generate and display the calendar
                generateCalendar(currentMonth, currentYear, data.schedules);
            }
        </script>
    @endpush
</x-app-layout>
