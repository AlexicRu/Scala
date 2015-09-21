<aside>
    <?
    foreach($menu as $link => $item){
        echo "<a href='/{$link}/'><span class='{$item['icon']}'></span> {$item['title']}</a>";
    }
    ?>
</aside>