<main class="d-flex flex-column main-page">
    <!-- HEADER PAGE -->
    <header class="header-page">
        <?php
        $pageHeader = new Pagebuilder();
        $pageHeader->module = "public";
        $pageHeader->folder = "template/view";
        $pageHeader->file = "header";
        include $pageHeader->loudPAGEParts();
        ?>
    </header>
    <!-- CONTAINT MODULE -->
    <section class="d-flex justify-content-center section-module">
        <?php
        $controller = new Controladora();
        include $controller->controller_page();
        ?>
    </section>
    <!-- FOOTER PAGE -->
    <footer class="footer-page">
        <?php
        $loudFOOTERPAGE = new Pagebuilder();
        $loudFOOTERPAGE->module = "public";
        $loudFOOTERPAGE->folder = "template/view";
        $loudFOOTERPAGE->file = "footer";
        include $loudFOOTERPAGE->loudPAGEParts();
        ?>
    </footer>
    <!-- MODALS -->
    <!--
    <div id="setModal" class="justify-content-center align-items-center modal-container-fixed d-none">
        <?php
        /*
        // LOUD LOGIN MODAL
        $modal = new Pagebuilder();
        $modal->module = "modal";
        $modal->folder = "template/view";
        $modal->file = "modal-login";
        include $modal->loudPAGEParts();
        // LOUD GENERIC MODAL
        $gModal = new Pagebuilder();
        $gModal->module = "modal";
        $gModal->folder = "template/view";
        $gModal->file = "modal-generic";
        include $gModal->loudPAGEParts();
        $modalRelatorioGen = new Pagebuilder();
        $modalRelatorioGen->module = "logistica";
        $modalRelatorioGen->folder = "template/view";
        $modalRelatorioGen->file = "retorno-nfe-gerar-relatorio";
        include $modalRelatorioGen->loudTemplatePHP_parts();
        */
        ?>
        -->
    </div>
</main>