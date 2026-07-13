document.addEventListener('DOMContentLoaded', () => {
    const today = new Date();
    let currentMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    let selectedDate = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`;

    const calendarDays = document.getElementById('calendarDays');
    const calendarMonthLabel = document.getElementById('calendarMonthLabel');
    const prevMonthBtn = document.getElementById('prevMonthBtn');
    const nextMonthBtn = document.getElementById('nextMonthBtn');
    const todayLabel = document.getElementById('todayLabel');

    function renderCalendar(date) {
        const year = date.getFullYear();
        const month = date.getMonth();
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        calendarMonthLabel.textContent = date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        calendarDays.innerHTML = '';

        for (let i = 0; i < firstDay; i++) {
            const blank = document.createElement('div');
            blank.textContent = '';
            calendarDays.appendChild(blank);
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const cell = document.createElement('button');
            cell.type = 'button';
            cell.textContent = day;
            const cellDate = new Date(year, month, day);
            const cellValue = `${cellDate.getFullYear()}-${String(cellDate.getMonth() + 1).padStart(2, '0')}-${String(cellDate.getDate()).padStart(2, '0')}`;
            cell.dataset.date = cellValue;

            if (cellValue === selectedDate) {
                cell.classList.add('selected-day');
            }

            cell.addEventListener('click', () => {
                selectedDate = cellValue;
                renderCalendar(currentMonth);
                window.dispatchEvent(new CustomEvent('day-selected', { detail: { date: cellValue } }));
            });

            const indicator = document.createElement('span');
            indicator.className = 'calendar-dot';
            cell.appendChild(indicator);
            calendarDays.appendChild(cell);
        }
    }

    async function loadCalendarIndicators() {
        try {
            const response = await fetch('./api/get_expenses.php');
            const data = await response.json();
            if (!data.success) return;

            const datesWithExpenses = new Set(data.expenses.map((expense) => expense.date_key));
            document.querySelectorAll('#calendarDays button').forEach((button) => {
                const buttonDate = button.dataset.date;
                if (buttonDate && datesWithExpenses.has(buttonDate)) {
                    button.classList.add('has-expense');
                }
            });
        } catch (error) {
            console.error(error);
        }
    }

    prevMonthBtn.addEventListener('click', () => {
        currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() - 1, 1);
        renderCalendar(currentMonth);
    });

    nextMonthBtn.addEventListener('click', () => {
        currentMonth = new Date(currentMonth.getFullYear(), currentMonth.getMonth() + 1, 1);
        renderCalendar(currentMonth);
    });

    window.addEventListener('expense-updated', async () => {
        renderCalendar(currentMonth);
        await loadCalendarIndicators();
    });

    renderCalendar(currentMonth);
    loadCalendarIndicators();
    todayLabel.textContent = new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
});
