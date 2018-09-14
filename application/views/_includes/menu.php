<aside class="scroll">
    <div>
    <?
    foreach($menu as $link => $item){
        if(Access::allow($link.'_index', true)) {

            $isActiveController = Text::camelCaseToDashed(Request::current()->controller()) == $link ;

            if (!empty($item['children'])) {
                foreach($item['children'] as $child => $name){
                    if(Access::deny($link.'_'.$child, true)) {
                        unset($item['children'][$child]);
                    }
                }
            }

            if(empty($item['children'])){
                echo "<a href='/{$link}' class='" . ($isActiveController ? 'act' : '') . " menu_item_{$link}'><span class='{$item['icon']}'></span> {$item['title']}</a>";
                continue;
            }

            $menuItem = "<div class='sub_menu'><div class='menu_item_{$link} sub_menu_title " . ($isActiveController == $link ? 'act' : '') . "'><span class='{$item['icon']}'></span> {$item['title']}</div>";

            if(!empty($item['children'])){
                $menuItem .= '<div class="sub_menu_items" '.($isActiveController ? 'style="display:block"' : '').'>';

                foreach($item['children'] as $child => $name){
                    $isActiveAction = Text::camelCaseToDashed(Request::current()->action()) == $child  && $isActiveController;

                    if(Access::allow($link.'_'.$child, true)) {
                        $menuItem .= '<div><a href="/'.$link.($child == 'index' ? '' : '/'.$child).'" class="menu_item_'.$link.'_'.$child.' '.($isActiveAction ? 'act' : '').'">'.$name.'</a></div>';
                    }
                }
                $menuItem .= '</div>';
            }

            echo $menuItem.'</div>';
        }
    }
    ?>
    </div>
    <div class="copyright">
        &copy; GloPro 2015-<?=date('Y')?>
    </div>
</aside>

<script>
    $(function () {
        $('.sub_menu_title').on('click', function () {
            var t = $(this);

            t.closest('.sub_menu').find('.sub_menu_items').slideToggle();
        });
    });
</script>