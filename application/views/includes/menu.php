<aside>
    <?
    foreach($menu as $link => $item){
        if(Access::allow($link.'_index')) {
            echo "<a href='/{$link}' class='" . (strtolower(Request::current()->controller()) == $link ? 'act' : '') . "'><span class='{$item['icon']}'></span> {$item['title']}</a>";
        }
    }
    ?>
</aside>