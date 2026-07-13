<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Expense Tracker</title>
    <style>
        :root {
            --bg: #07111f;
            --panel: rgba(255, 255, 255, 0.14);
            --panel-strong: rgba(255, 255, 255, 0.2);
            --text: #f8fbff;
            --muted: #a8b4c7;
            --accent: #5eead4;
            --accent-2: #60a5fa;
            --danger: #fb7185;
            --shadow: 0 16px 45px rgba(0, 0, 0, 0.28);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a, #1e3a8a 55%, #0f766e);
            color: var(--text);
            padding: 20px;
        }

        .app-shell {
            display: grid;
            grid-template-columns: 260px 1fr;
            gap: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .sidebar, .main-panel {
            background: var(--panel);
            box-shadow: var(--shadow);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 24px;
        }

        .sidebar {
            padding: 24px 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 30px;
        }

        .brand-icon {
            width: 45px;
            height: 45px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            display: grid;
            place-items: center;
            font-size: 1.2rem;
            font-weight: bold;
            color: #05233d;
        }

        .brand h2 {
            font-size: 1.05rem;
            font-weight: 700;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .nav-links a {
            color: var(--text);
            text-decoration: none;
            padding: 12px 14px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: 0.25s ease;
        }

        .nav-links a:hover, .nav-links a.active {
            background: rgba(255, 255, 255, 0.16);
            transform: translateX(2px);
        }

        .logout-btn {
            margin-top: 20px;
            border: none;
            padding: 12px 14px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--danger), #f97316);
            color: white;
            cursor: pointer;
            font-weight: 600;
        }

        .main-panel {
            padding: 24px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .topbar h1 {
            font-size: 1.6rem;
        }

        .topbar p {
            color: var(--muted);
            margin-top: 4px;
        }

        .greeting-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, rgba(94,234,212,0.28), rgba(96,165,250,0.25));
            border-radius: 20px;
            padding: 22px 24px;
            margin-bottom: 20px;
        }

        .greeting-card h3 {
            font-size: 1.25rem;
            margin-bottom: 6px;
        }

        .greeting-card p {
            color: #dfe8f7;
        }

        .date-pill {
            background: rgba(255, 255, 255, 0.16);
            padding: 10px 14px;
            border-radius: 999px;
            font-weight: 600;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .summary-card {
            padding: 18px;
            border-radius: 16px;
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.16);
        }

        .summary-card .label {
            color: var(--muted);
            font-size: 0.92rem;
            margin-bottom: 8px;
        }

        .summary-card .value {
            font-size: 1.35rem;
            font-weight: 700;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
        }

        .calendar-card, .expenses-card {
            background: rgba(255,255,255,0.12);
            padding: 20px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.16);
        }

        .calendar-card h3, .expenses-card h3 {
            margin-bottom: 12px;
            font-size: 1.08rem;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            color: #e3eefc;
        }

        .calendar-nav {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .calendar-nav button {
            border: none;
            background: rgba(255,255,255,0.16);
            color: white;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 1rem;
        }

        .calendar-weekdays, .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
        }

        .calendar-weekdays div {
            text-align: center;
            color: var(--muted);
            font-size: 0.9rem;
            padding-bottom: 6px;
        }

        .calendar-days button {
            border: none;
            text-align: center;
            padding: 10px 0;
            border-radius: 10px;
            background: rgba(255,255,255,0.06);
            min-height: 44px;
            color: white;
            cursor: pointer;
            transition: 0.2s ease;
            position: relative;
        }

        .calendar-days button:hover {
            background: rgba(255,255,255,0.16);
            transform: translateY(-1px);
        }

        .calendar-days .today {
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            color: #04111f;
            font-weight: 700;
        }

        .calendar-days .selected-day {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
        }

        .calendar-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--accent);
            display: none;
            position: absolute;
            bottom: 6px;
            left: 50%;
            transform: translateX(-50%);
        }

        .calendar-days button.has-expense .calendar-dot {
            display: block;
        }

        .expense-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .expense-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.12);
        }

        .expense-item:last-child {
            border-bottom: none;
        }

        .expense-info strong {
            display: block;
            margin-bottom: 3px;
        }

        .expense-info span {
            display: block;
            color: var(--muted);
            font-size: 0.92rem;
            margin-top: 2px;
        }

        .expense-amount {
            font-weight: 700;
            color: #ffd6a5;
        }

        .empty-state {
            color: var(--muted);
            padding: 12px 0;
            font-size: 0.95rem;
        }

        .toast-container {
            position: fixed;
            right: 20px;
            bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 1100;
        }

        .toast {
            min-width: 220px;
            max-width: 320px;
            padding: 12px 14px;
            border-radius: 12px;
            background: rgba(15, 23, 42, 0.95);
            color: white;
            box-shadow: var(--shadow);
            opacity: 0;
            transform: translateY(8px);
            transition: all 0.25s ease;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        .toast.error {
            border-left: 4px solid var(--danger);
        }

        .toast.success {
            border-left: 4px solid var(--accent);
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            display: none;
            justify-content: center;
            align-items: center;
            padding: 20px;
            z-index: 1000;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal {
            width: min(480px, 100%);
            background: #10233d;
            border-radius: 20px;
            padding: 24px;
            box-shadow: var(--shadow);
            position: relative;
        }

        .modal-close {
            position: absolute;
            top: 12px;
            right: 12px;
            border: none;
            background: transparent;
            color: white;
            font-size: 1.3rem;
            cursor: pointer;
        }

        .modal h3 {
            margin-bottom: 16px;
        }

        .modal form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .modal label {
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: 0.95rem;
            color: #dce9f8;
        }

        .modal input, .modal select, .modal textarea {
            border: 1px solid rgba(255,255,255,0.16);
            border-radius: 10px;
            padding: 10px 12px;
            background: rgba(255,255,255,0.08);
            color: white;
        }

        .modal textarea {
            min-height: 80px;
            resize: vertical;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 8px;
        }

        .modal-actions button {
            border: none;
            padding: 10px 14px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
        }

        .modal-actions .cancel-btn {
            background: rgba(255,255,255,0.14);
            color: white;
        }

        .modal-actions .save-btn {
            background: linear-gradient(135deg, var(--accent), var(--accent-2));
            color: #04111f;
        }

        @media (max-width: 1100px) {
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            body {
                padding: 12px;
            }

            .app-shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                padding: 16px;
            }

            .nav-links {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

            .greeting-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <div>
                <div class="brand">
                    <div class="brand-icon">$</div>
                    <div>
                        <h2>ExpenseFlow</h2>
                        <p style="color: var(--muted); font-size: 0.9rem;">Daily Tracker</p>
                    </div>
                </div>

                <nav class="nav-links">
                    <a href="#" class="active">📊 Dashboard</a>
                    <a href="#">💸 Expenses</a>
                    <a href="#">📈 Reports</a>
                    <a href="#">🎯 Budget</a>
                    <a href="#">👤 Profile</a>
                </nav>
            </div>

            <button class="logout-btn">Logout</button>
        </aside>

        <main class="main-panel">
            <div class="topbar">
                <div>
                    <h1>Dashboard</h1>
                    <p>Track your daily spending with clarity</p>
                </div>
                <div class="date-pill" id="currentDate">Loading...</div>
            </div>

            <section class="greeting-card">
                <div>
                    <h3 id="greetingText">Good morning!</h3>
                    <p>You're doing great. Keep your spending on track today.</p>
                </div>
                <div class="date-pill" id="todayLabel">Today</div>
            </section>

            <section class="summary-grid">
                <div class="summary-card">
                    <div class="label">Expenses</div>
                    <div class="value" id="expensesValue">$0.00</div>
                </div>
                <div class="summary-card">
                    <div class="label">Remaining Balance</div>
                    <div class="value" id="remainingBalanceValue">$0.00</div>
                    <select id="balanceFilter" style="margin-top:10px; width:100%; padding:8px; border-radius:8px; border:1px solid rgba(255,255,255,0.16); background:rgba(255,255,255,0.08); color:white;">
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                <div class="summary-card">
                    <div class="label">Today's Expenses</div>
                    <div class="value" id="todayExpensesValue">$0.00</div>
                </div>
                <div class="summary-card">
                    <div class="label">Expenses Filter</div>
                    <div class="value" id="expensesFilterValue">$0.00</div>
                    <select id="expenseFilter" style="margin-top:10px; width:100%; padding:8px; border-radius:8px; border:1px solid rgba(255,255,255,0.16); background:rgba(255,255,255,0.08); color:white;">
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
            </section>

            <div style="margin-bottom: 20px;">
                <button type="button" id="openExpenseModalBtn" class="logout-btn" style="padding: 12px 18px;">+ Add Expense</button>
            </div>

            <section class="content-grid">
                <div class="calendar-card">
                    <div class="calendar-header">
                        <h3 id="calendarTitle">Monthly Calendar</h3>
                        <div class="calendar-nav">
                            <button type="button" id="prevMonthBtn" aria-label="Previous month">‹</button>
                            <span id="calendarMonthLabel"></span>
                            <button type="button" id="nextMonthBtn" aria-label="Next month">›</button>
                        </div>
                    </div>
                    <div class="calendar-weekdays">
                        <div>Sun</div>
                        <div>Mon</div>
                        <div>Tue</div>
                        <div>Wed</div>
                        <div>Thu</div>
                        <div>Fri</div>
                        <div>Sat</div>
                    </div>
                    <div class="calendar-days" id="calendarDays"></div>
                </div>

                <div class="expenses-card">
                    <h3 id="dailySummaryTitle">Daily Summary</h3>
                    <div id="dailySummaryContent" class="expense-list"></div>
                    <h3 style="margin-top: 18px;">Recent Expenses</h3>
                    <div class="expense-list" id="expenseList"></div>
                </div>
            </section>
        </main>
    </div>

    <div class="modal-overlay" id="expenseModal">
        <div class="modal">
            <button class="modal-close" id="closeModalBtn" type="button">×</button>
            <h3>Add Expense</h3>
            <form id="expenseForm">
                <input type="hidden" id="expenseDateValue" name="date">
                <label>
                    Date
                    <input type="text" id="expenseDateDisplay" readonly>
                </label>
                <label>
                    Category
                    <select id="expenseCategory" required>
                        <option value="">Select category</option>
                        <option>Food</option>
                        <option>Transport</option>
                        <option>Utilities</option>
                        <option>Shopping</option>
                        <option>Health</option>
                    </select>
                </label>
                <label>
                    Description
                    <textarea id="expenseDescription" placeholder="Enter details" required></textarea>
                </label>
                <label>
                    Amount
                    <input type="number" id="expenseAmount" step="0.01" placeholder="0.00" required>
                </label>
                <label>
                    Payment Method
                    <select id="expenseMethod" required>
                        <option value="">Select method</option>
                        <option>Cash</option>
                        <option>Card</option>
                        <option>Online</option>
                    </select>
                </label>
                <div class="modal-actions">
                    <button type="button" class="cancel-btn" id="cancelBtn">Cancel</button>
                    <button type="submit" class="save-btn">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div class="toast-container" id="toastContainer"></div>
    <script src="./assets/js/dashboard.js"></script>
    <script src="./assets/js/calendar.js"></script>
</body>
</html>