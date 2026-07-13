document.addEventListener('DOMContentLoaded', () => {
    const today = new Date();
    let currentMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    let selectedDate = formatToDateInput(today);

    const currentDate = document.getElementById('currentDate');
    const todayLabel = document.getElementById('todayLabel');
    const greetingText = document.getElementById('greetingText');
    const expenseModal = document.getElementById('expenseModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const expenseDateDisplay = document.getElementById('expenseDateDisplay');
    const expenseDateValue = document.getElementById('expenseDateValue');
    const expenseForm = document.getElementById('expenseForm');
    const expenseList = document.getElementById('expenseList');
    const toastContainer = document.getElementById('toastContainer');
    const dailySummaryContent = document.getElementById('dailySummaryContent');
    const dailySummaryTitle = document.getElementById('dailySummaryTitle');
    const openExpenseModalBtn = document.getElementById('openExpenseModalBtn');
    const balanceFilter = document.getElementById('balanceFilter');
    const expenseFilter = document.getElementById('expenseFilter');

    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    currentDate.textContent = today.toLocaleDateString('en-US', options);
    todayLabel.textContent = today.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });

    const hour = today.getHours();
    if (hour < 12) greetingText.textContent = 'Good morning!';
    else if (hour < 18) greetingText.textContent = 'Good afternoon!';
    else greetingText.textContent = 'Good evening!';

    function openExpenseModal(selectedDateValue) {
        const displayDate = selectedDateValue.toLocaleDateString('en-US', {
            weekday: 'short',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        expenseDateDisplay.value = displayDate;
        expenseDateValue.value = formatToDateInput(selectedDateValue);
        expenseModal.classList.add('active');
    }

    function formatToDateInput(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    function closeExpenseModal() {
        expenseModal.classList.remove('active');
        expenseForm.reset();
        expenseDateDisplay.value = '';
        expenseDateValue.value = '';
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;
        toastContainer.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('show');
        }, 10);

        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, 2800);
    }

    async function refreshDashboard() {
        try {
            const balancePeriod = balanceFilter.value;
            const expensePeriod = expenseFilter.value;
            const [balanceResponse, expenseResponse] = await Promise.all([
                fetch(`./api/update_dashboard.php?period=${balancePeriod}`),
                fetch(`./api/update_dashboard.php?period=${expensePeriod}`)
            ]);

            const balanceData = await balanceResponse.json();
            const expenseData = await expenseResponse.json();

            if (!balanceData.success || !expenseData.success) {
                throw new Error('Unable to load dashboard data.');
            }

            document.getElementById('remainingBalanceValue').textContent = `$${Number(balanceData.remaining_balance).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            document.getElementById('expensesValue').textContent = `$${Number(expenseData.expenses).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            document.getElementById('expensesFilterValue').textContent = `$${Number(expenseData.expenses).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            document.getElementById('todayExpensesValue').textContent = `$${Number(await getTodayExpenses()).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        } catch (error) {
            console.error(error);
        }
    }

    async function getTodayExpenses() {
        const response = await fetch('./api/get_daily_summary.php?date=' + formatToDateInput(new Date()));
        const data = await response.json();
        return data.success ? data.total : '0.00';
    }

    async function refreshExpenses() {
        try {
            const response = await fetch('./api/get_expenses.php');
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Unable to load expenses.');
            }

            expenseList.innerHTML = '';

            if (!data.expenses.length) {
                const empty = document.createElement('div');
                empty.className = 'empty-state';
                empty.textContent = 'No expenses yet. Add your first expense to get started.';
                expenseList.appendChild(empty);
                return;
            }

            const fragment = document.createDocumentFragment();

            data.expenses.forEach((expense) => {
                const item = document.createElement('div');
                item.className = 'expense-item';
                item.innerHTML = `
                    <div class="expense-info">
                        <strong>${expense.category}</strong>
                        <span>${expense.description}</span>
                        <span>${expense.date} • ${expense.payment_method}</span>
                    </div>
                    <div class="expense-amount">-$${Number(expense.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div>
                `;
                fragment.appendChild(item);
            });

            expenseList.appendChild(fragment);
        } catch (error) {
            console.error(error);
        }
    }

    async function refreshDailySummary() {
        try {
            const response = await fetch('./api/get_daily_summary.php?date=' + selectedDate);
            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Unable to load daily summary.');
            }

            dailySummaryTitle.textContent = `Daily Summary • ${data.date}`;
            dailySummaryContent.innerHTML = '';

            if (!data.expenses.length) {
                dailySummaryContent.innerHTML = '<div class="empty-state">No expenses for this day.</div>';
                return;
            }

            const summaryHeader = document.createElement('div');
            summaryHeader.innerHTML = `
                <div class="summary-card" style="margin-bottom: 10px; padding: 12px;">
                    <div class="label">Total Expenses</div>
                    <div class="value">$${Number(data.total).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</div>
                    <div class="label" style="margin-top: 8px;">Transactions: ${data.transactions}</div>
                </div>
            `;
            dailySummaryContent.appendChild(summaryHeader.firstChild);

            const table = document.createElement('table');
            table.style.width = '100%';
            table.style.borderCollapse = 'collapse';
            table.innerHTML = '<thead><tr><th style="text-align:left; padding:6px 0; color:#a8b4c7;">Category</th><th style="text-align:left; padding:6px 0; color:#a8b4c7;">Description</th><th style="text-align:left; padding:6px 0; color:#a8b4c7;">Payment</th><th style="text-align:right; padding:6px 0; color:#a8b4c7;">Amount</th></tr></thead><tbody></tbody>';
            const tbody = table.querySelector('tbody');
            data.expenses.forEach((expense) => {
                const row = document.createElement('tr');
                row.innerHTML = `<td style="padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,0.12);">${expense.category}</td><td style="padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,0.12);">${expense.description}</td><td style="padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,0.12);">${expense.payment_method}</td><td style="padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,0.12); text-align:right;">$${Number(expense.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>`;
                tbody.appendChild(row);
            });
            dailySummaryContent.appendChild(table);
        } catch (error) {
            console.error(error);
        }
    }

    async function refreshAll() {
        await Promise.all([refreshDashboard(), refreshExpenses(), refreshDailySummary()]);
        window.dispatchEvent(new Event('expense-updated'));
    }

    closeModalBtn.addEventListener('click', closeExpenseModal);
    cancelBtn.addEventListener('click', closeExpenseModal);
    expenseModal.addEventListener('click', (event) => {
        if (event.target === expenseModal) {
            closeExpenseModal();
        }
    });

    openExpenseModalBtn.addEventListener('click', () => openExpenseModal(new Date(selectedDate)));

    balanceFilter.addEventListener('change', refreshDashboard);
    expenseFilter.addEventListener('change', refreshDashboard);

    expenseForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const payload = {
            date: expenseDateValue.value,
            category: document.getElementById('expenseCategory').value,
            description: document.getElementById('expenseDescription').value.trim(),
            amount: document.getElementById('expenseAmount').value,
            payment_method: document.getElementById('expenseMethod').value
        };

        try {
            const response = await fetch('./api/add_expense.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (!data.success) {
                showToast(data.message || 'Unable to save expense.', 'error');
                return;
            }

            showToast(data.message || 'Expense saved successfully.');
            closeExpenseModal();
            selectedDate = payload.date;
            await refreshAll();
        } catch (error) {
            showToast('Unable to connect to the server.', 'error');
            console.error(error);
        }
    });

    window.addEventListener('day-selected', (event) => {
        selectedDate = event.detail.date;
        refreshDailySummary();
    });

    refreshAll();
});
