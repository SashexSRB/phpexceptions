
<h1>Home</h1>
<p>Welcome back, <?=htmlspecialchars($_SESSION['account_name'], ENT_QUOTES)?>!</p>
<p>This is the home page. You are logged in!</p>

<div class="forms">
    <form method="POST" action="/home">
        <h2>Deposit</h2>
        <input type="hidden" name="action" value="deposit" required>
        <label>Amount: <input type="number" name="amount" required></label>
        <button type="submit">Deposit</button>
    </form>

    
    <form method="POST" action="/home">
        <h2>Transfer</h2>
        <input type="hidden" name="action" value="transfer"required>
        <label>Username: <input type="text" name="username" required></label>
        <label>Amount: <input type="number" name="amount" required></label>
        <button type="submit">Transfer</button>
    </form>
</div>

<h2>Your Transactions</h2>
<table>
    <tr>
        <th>Source</th>
        <th>Destination</th>
        <th>Amount</th>
        <th>Date</th>
    <?php foreach ($data['transactions'] as $transaction): ?>
        <tr>
            <td><?php echo htmlspecialchars($transaction->getSource()); ?></td>
            <td><?php echo htmlspecialchars($transaction->getDestination()); ?></td>
            <td><?php echo htmlspecialchars($transaction->getAmount()); ?></td>
            <td><?php echo htmlspecialchars($transaction->getTimestamp()); ?></td>
        </tr>
    <?php endforeach; ?>
    </tr>
</table>



