<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sell Item</title>
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/form.css">
    <link rel="stylesheet" href="../css/sell.css">
    <link rel="stylesheet" href="../css/top_nav.css">
    <script src="../js/sell.js"></script>
</head>
<body>

<nav class="top_nav">
    <a href="main_page.php">Home</a>
    <a href="user_information.php">User info</a>
    <a href="transaction_history.php">Transaction history</a>
    <a href="logout.php">Logout</a>
</nav>

<main>

    <div class="title">Sell your item!</div>
    <form method="post" action="sell.php" id="sell_form">

        <div class="item_details">

            <div class="input_box">
                <label for="item_name">Name</label>
                <input type="text" name="sell_item_name" id="item_name" tabindex="1" autofocus>
            </div>

            <div class="input_box">
                <label for="item_price">Price</label>
                <input type="text" name="sell_item_price" id="item_price" tabindex="2">
            </div>

            <div class="input_box">
                <label for="item_pic">Picture</label>
                <input type="file" name="sell_item_pic" id="item_pic" accept="image/png" tabindex="3">
            </div>

            <div class="input_box">

                <label for="categories">Choose <strong>1 - 4</strong> categories</label>
                <br>
                <select name="sell_item_categories" id="categories" multiple>
                    <option tabindex="4" value="Tee"></option>
                    <option value="sHOES">sHOES</option>
                    <option value="bnig">bnig</option>
                    <option value="pejčka">pejčka</option>
                    <option value="mrdkoška">mrdkoška</option>
                    <option value="kacafir">kacafir</option>
                </select>
            </div>

            <div id="selected_categories"></div>

            <div class="button">
                <input type="submit" value="Insert listing">

            </div>

        </div>

    </form>

</main>

</body>
</html>