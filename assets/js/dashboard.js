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
    const toastContainer = document.getElementById('toastContainer');
    const dailySummaryContent = document.getElementById('dailySummaryContent');
    const dailySummaryTitle = document.getElementById('dailySummaryTitle');
    const openExpenseModalBtn = document.getElementById('openExpenseModalBtn');
    const budgetTypeSelect = document.getElementById('budgetTypeSelect');
    const budgetAmountInput = document.getElementById('budgetAmount');
    const saveBudgetBtn = document.getElementById('saveBudgetBtn');
    const remainingPeriodLabel = document.getElementById('remainingPeriodLabel');
    const budgetTypeDefault = localStorage.getItem('currentBudgetType') || 'weekly';

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
            // Update today's expenses (for selected date) and remaining budget
            document.getElementById('todayExpensesValue').textContent = `$${Number(await getTodayExpenses()).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            await updateRemainingBudget();
        } catch (error) {
            console.error(error);
        }
    }

    async function getTodayExpenses() {
        // Use the currently selected date (not always today's date)
        const response = await fetch('./api/get_daily_summary.php?date=' + selectedDate);
        const data = await response.json();
        return data.success ? data.total : '0.00';
    }

    // Recent expenses list removed — Daily Summary now provides the date-specific table and totals.

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
                dailySummaryContent.innerHTML = '<div class="empty-state">No expenses for this day.</div>' +
                    '<div style="margin-top:12px; font-weight:700;">Total Expenses: $0.00</div>';
                return;
            }

            const table = document.createElement('table');
            table.style.width = '100%';
            table.style.borderCollapse = 'collapse';
            table.innerHTML = '<thead><tr><th style="text-align:left; padding:6px 0; color:#a8b4c7;">Category</th><th style="text-align:left; padding:6px 0; color:#a8b4c7;">Description</th><th style="text-align:left; padding:6px 0; color:#a8b4c7;">Payment Method</th><th style="text-align:right; padding:6px 0; color:#a8b4c7;">Amount</th></tr></thead><tbody></tbody>';
            const tbody = table.querySelector('tbody');
            data.expenses.forEach((expense) => {
                const row = document.createElement('tr');
                row.innerHTML = `<td style="padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,0.12);">${expense.category}</td><td style="padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,0.12);">${expense.description}</td><td style="padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,0.12);">${expense.payment_method}</td><td style="padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,0.12); text-align:right;">$${Number(expense.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>`;
                tbody.appendChild(row);
            });
            dailySummaryContent.appendChild(table);

            const totalDiv = document.createElement('div');
            totalDiv.style.marginTop = '12px';
            totalDiv.style.fontWeight = '700';
            totalDiv.textContent = `Total Expenses: $${Number(data.total).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            dailySummaryContent.appendChild(totalDiv);
        } catch (error) {
            console.error(error);
        }
    }

    async function loadBudgets() {
        try {
            const res = await fetch('./api/get_budget.php');
            const data = await res.json();
            if (!data.success) return {};
            return data.budgets || {};
        } catch (e) {
            console.error(e);
            return {};
        }
    }

    async function saveBudget(type, amount) {
        try {
            const res = await fetch('./api/save_budget.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ type, amount })
            });
            return await res.json();
        } catch (e) {
            console.error(e);
            return { success: false };
        }
    }

    async function updateRemainingBudget() {
        try {
            const currentType = localStorage.getItem('currentBudgetType') || budgetTypeDefault || 'weekly';
            // load saved budget amount
            const budgetRes = await fetch('./api/get_budget.php?type=' + currentType);
            const budgetData = await budgetRes.json();
            const budgetAmount = budgetData.success ? parseFloat(budgetData.amount) : 0;

            // get expenses for period using existing API
            const periodRes = await fetch(`./api/update_dashboard.php?period=${currentType}`);
            const periodData = await periodRes.json();
            const periodExpenses = periodData.success ? parseFloat(periodData.expenses) : 0;

            const remaining = budgetAmount - periodExpenses;

            document.getElementById('remainingBudgetValue').textContent = `$${Number(remaining).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            remainingPeriodLabel.textContent = `Using: ${currentType.charAt(0).toUpperCase() + currentType.slice(1)}`;
        } catch (e) {
            console.error(e);
        }
    }

    async function refreshAll() {
        await Promise.all([refreshDashboard(), refreshDailySummary()]);
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

    // Initialize budget controls
    (async () => {
        budgetTypeSelect.value = budgetTypeDefault;
        localStorage.setItem('currentBudgetType', budgetTypeDefault);
        const budgets = await loadBudgets();
        if (budgets[budgetTypeDefault]) {
            budgetAmountInput.value = budgets[budgetTypeDefault];
        } else {
            budgetAmountInput.value = '';
        }
        await updateRemainingBudget();
    })();

    saveBudgetBtn.addEventListener('click', async () => {
        const type = budgetTypeSelect.value;
        const amount = parseFloat(budgetAmountInput.value) || 0;
        const res = await saveBudget(type, amount);
        if (res.success) {
            showToast('Budget saved.');
            localStorage.setItem('currentBudgetType', type);
            await refreshAll();
        } else {
            showToast(res.message || 'Unable to save budget.', 'error');
        }
    });

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
        // Refresh everything that depends on the selected date
        refreshAll();
    });

    refreshAll();
});
