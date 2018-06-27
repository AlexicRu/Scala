<aside class="scroll">
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
                echo "<a href='/{$link}' class='" . ($isActiveController ? 'act' : '') . "'><span class='{$item['icon']}'></span> {$item['title']}</a>";
                continue;
            }

            $menuItem = "<div class='sub_menu'><div class='sub_menu_title " . ($isActiveController == $link ? 'act' : '') . "'><span class='{$item['icon']}'></span> {$item['title']}</div>";

            if(!empty($item['children'])){
                $menuItem .= '<div class="sub_menu_items" '.($isActiveController ? 'style="display:block"' : '').'>';

                foreach($item['children'] as $child => $name){
                    $isActiveAction = Text::camelCaseToDashed(Request::current()->action()) == $child  && $isActiveController;

                    if(Access::allow($link.'_'.$child, true)) {
                        $menuItem .= '<div><a href="/'.$link.($child == 'index' ? '' : '/'.$child).'" class="'.($isActiveAction ? 'act' : '').'">'.$name.'</a></div>';
                    }
                }
                $menuItem .= '</div>';
            }

            echo $menuItem.'</div>';
        }
    }
    ?>
</aside>

<script>
    $(function () {
        $('.sub_menu_title').on('click', function () {
            var t = $(this);

            t.closest('.sub_menu').find('.sub_menu_items').slideToggle();
        });
    });
</script>