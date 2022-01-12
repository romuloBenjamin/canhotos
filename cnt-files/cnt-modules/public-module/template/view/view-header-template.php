<section class="d-flex justify-content-between first-container-header-page">
    <div class="d-flex coluna-01">
        <!-- LOGO -->
        <div class="d-flex logo">
            <a id="logoSales" href="javascript: void(0);">
                <img src="<?= DIR_PATH; ?>images/logo/logo_min.png" alt="Logo_Sales">
            </a>
        </div>
        <!-- PESQUISA -->
        <div class="d-flex form-container">
            <?php
            $setMainForm = new Pagebuilder();
            $setMainForm->attributes = "{\"attributes\":[{\"id\":\"pesquisar\"},{\"id\":\"pesquisarFuncionario\", \"name\":\"pesquisarFuncionario\"}]}";
            $setMainForm->module = "search";
            $setMainForm->folder = "template/forms";
            $setMainForm->file = "pesquisa";
            include $setMainForm->loudPAGEParts();
            ?>
            <div class="d-none pesquisarResultset">
                <ul class="list-group"></ul>
            </div>
        </div>
    </div>
    <div class="d-flex flex-column coluna-02">
        <!-- LOGIN -->
        <a class="d-flex justify-content-center login-pseudo-btn">
            <div class="d-flex profileName" id="profineUser">Login</div>
            <span class="d-flex profineImage" id="profineAvatar"><i class="far fa-user-circle"></i></span>
        </a>
        <div class="d-none loginOptions" id="loginOptions"></div>
    </div>
</section>
<section class="d-flex justify-content-center second-container-header-page">
    <div class="d-flex inner-container-header">
        <div class="d-flex container-menu">
            <?php
            $setMenuPrimary = new Pagebuilder();
            $setMenuPrimary->module = "menu";
            $setMenuPrimary->folder = "template/view";
            $setMenuPrimary->file = "menu-botao-retorno";
            $setMenuPrimary->extension = true;
            include $setMenuPrimary->loudPAGEParts();
            ?>
        </div>
        <div class="d-none form-container google-search">
            <?php
            $setFromGoogle = new Pagebuilder();
            $setFromGoogle->module = "search";
            $setFromGoogle->folder = "template/forms";
            $setFromGoogle->file = "pesquisa-google";
            include $setFromGoogle->loudPAGEParts();
            ?>
        </div>
    </div>
</section>
<!--<script src="cnt-modules/login-module/js/login-js.js"></script>-->