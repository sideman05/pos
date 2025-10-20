<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - POS</title>
    <style>
        :root {
            --accent: #205fa8f8;
            --bg: #f6fbf6
        }

        html,
        body {
            height: 100%;
            margin: 0
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, #eef7ef, #ffffff);
            font-family: system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, 'Helvetica Neue', Arial
        }

        .card {
            background: #fff;
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(10, 20, 30, 0.06);
            max-width: 720px;
            width: 94%;
            text-align: center
        }

        h1 {
            margin: 0 0 8px 0;
            font-size: 1.6rem;
            color: #0b3d13
        }

        p {
            margin: 0 0 16px;
            color: #28502a
        }

        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 8px;
            text-decoration: none;
            color: #fff;
            background: var(--accent);
            font-weight: 600
        }

        .secondary {
            background: transparent;
            color: var(--accent);
            border: 2px solid rgba(43, 138, 62, 0.12)
        }

        .countdown {
            color: #666;
            margin-top: 10px
        }

        @media(max-width:480px) {
            h1 {
                font-size: 1.2rem
            }

            .card {
                padding: 18px
            }
        }
    </style>
    <script>
        let seconds = 5;

        function tick() {
            const el = document.getElementById('countdown');
            if (!el) return;
            el.textContent = seconds;
            if (seconds <= 0) {
                window.location.href = 'pos.php';
                return;
            }
            seconds--;
            setTimeout(tick, 1000);
        }
        document.addEventListener('DOMContentLoaded', tick);
    </script>
</head>

<body>
    <div class="card">
        <h1>Welcome to the POS</h1>
        <p>Quick access to point-of-sale. You'll be redirected to the POS main in <span id="countdown">5</span>s.</p>
        <div class="actions">
            <a class="btn" href="pos.php">Open POS</a>
            <a class="btn secondary" href="admin/dashboard.php">Admin Area</a>
        </div>
        <div class="countdown">Redirecting in <span id="countdown">5</span> seconds…</div>
    </div>
</body>

</html>