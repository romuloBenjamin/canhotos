<section class="d-fixed flex-row fixed-menus">
    <ul class="list-group list-group-horizontal">
        <li class="list-group-item border-0">
            <h2 id="fixedMenuTitle"></h2>
        </li>
        <!-- FIXED MENU CADASTRAR -->
        <li class="list-group-item border-0">
            <div id="fixedCadastrar" class="d-flex fixed-menu">
                <!-- ACTIONS -->
                <ul class="list-group list-group-horizontal">
                    <li class="list-group-item border-0">
                        <?php
                        $fixed_btn = new Pagebuilder();
                        $fixed_btn->module = "menu";
                        $fixed_btn->folder = "template/buttons";
                        $fixed_btn->file = "voltar";
                        include $fixed_btn->loudTemplateHTML_parts();
                        ?>
                    </li>
                    <li class="list-group-item border-0">
                        <?php
                        $fixed_btn = new Pagebuilder();
                        $fixed_btn->module = "menu";
                        $fixed_btn->folder = "template/buttons";
                        $fixed_btn->file = "salvar";
                        include $fixed_btn->loudTemplateHTML_parts();
                        ?>
                    </li>
                </ul>
            </div>
        </li>
        <!-- FIXED MENU CADASTRAR -->
    </ul>
</section>
<script src="<?= DIR_PATH; ?>cnt-modules/menu-module/js/listar-fixed-menus-js.js" defer></script>