<?php
?>

<ul style="list-style-type: square; font-weight: 600 !important; padding-left: 20px;">
    <li> <a href="/pg_index.php">home</a> </li>
    <li> <a href="/wrv/pg_index.php">wilmas reviews</a></li>
    <li> <a href="/sparky/pg_index.php">sparky</a></li>
    <li> <a href="/about/pg_index.php">about</a></li>


    <?php if (isset($_SESSION['user_pk'])): ?>
        <li style="list-style: none; margin-right: 25px; margin-top: 10px;"> <hr> </li>
        <li> <a href="/sports_anals/pg_index.php">sports</a></li>
    <?php endif; ?>

<!--    <li> <a href="/text_games/pg_index.php">text games</a></li>-->
<!--    <li> <a href="/for_sale/pg_index.php">for sale</a> </li>-->


    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
        <li style="list-style: none; margin-right: 25px; margin-top: 10px;"> <hr> </li>
        <li style=" margin-top: 10px;"> <a href="/admin/pg_index.php">admin</a> </li>
    <?php endif; ?>

    <li style="list-style: none; margin-right: 25px; margin-top: 10px;"> <hr> </li>
    <?php if (isset($_SESSION['user_pk'])): ?>
        <li  style=" margin-top: 10px;"> <a href="/account/pg_exists.php">account</a> </li>
        <li> <a href="/account/pg_logout.php">logout</a> </li>
    <?php else: ?>
        <li  style=" margin-top: 10px;"> <a href="/account/pg_login.php">login</a> </li>
    <?php endif; ?>
</ul>