
<h1>Home</h1>
<p>Welcome back, <?=htmlspecialchars($_SESSION['account_name'], ENT_QUOTES)?>!</p>
<p>This is the home page. You are logged in!</p>

<h2>Deposit</h2>
<form method="POST" action="/home">
    <label>Amount: <input type="number" name="amount" required></label>
    <button type="submit">Deposit</button>
</form>
