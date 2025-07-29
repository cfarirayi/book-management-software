<?php
/**
 * @var array $authors
 * @var array $currencies
 */
?>

<form method="get" action="">
    <table>
        <tr>
            <td>Author</td>
            <td>
                <select name="author_id" required>
                    <option value="">-- Select Author --</option>
                    <?php foreach ($authors as $author): ?>
                        <option value="<?= htmlspecialchars($author->id) ?>">
                            <?= htmlspecialchars($author->first_name . ' ' . $author->last_name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <td>Title</td>
            <td><input type="text" name="title" required /></td>
        </tr>

        <tr>
            <td>Currency</td>
            <td>
                <select name="currency_id" required>
                    <option value="">-- Select Currency --</option>
                    <?php foreach ($currencies as $currency): ?>
                        <option value="<?= htmlspecialchars($currency->id) ?>">
                            <?= htmlspecialchars($currency->iso) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr>
            <td>Price</td>
            <td><input type="number" name="price" step="0.01" min="0" required /></td>
        </tr>

        <tr>
            <td colspan="2" align="right">
                <input type="submit" value="Create" />
            </td>
        </tr>
    </table>
</form>
