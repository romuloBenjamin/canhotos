<section class="d-flex footer-menus justify-content-between">
    <!-- COLUNA 01 -->
    <div class="d-flex flex-column coluna coluna-01">
        <h3>Grupo Sales</h3>
        <p>Somos referência no segmento de distribuição de produtos descartáveis, de higiene e limpeza, e também somos os fabricantes das enceradeiras Cleaner.</p>
        <div class="d-flex menu-footer-container">
            <?php
            $menuFooter1 = new Pagebuilder();
            $menuFooter1->module = "menu";
            $menuFooter1->folder = "template/view";
            $menuFooter1->file = "menu-footer-coluna1";
            include $menuFooter1->loudPAGEParts();
            ?>
        </div>
    </div>
    <!-- COLUNA 02 -->
    <div class="d-flex flex-column coluna coluna-02">
        <h3>Endereço</h3>
        <p>Rua Palmeira Batuá, 199, Jd. Eliane<br />São Paulo - SP CEP: 03575-110</p>
        <hr>
        <h3>Contato</h3>
        <p><span class=""></span>Tel: 2723-4000 / Fax: 2723-3800<br /><span class=""></span>sac@cleaner.com.br<br />cleaner@cleaner.com.br</p>
    </div>
    <!-- COLUNA 03 -->
    <div class="d-flex flex-column coluna coluna-03">
        <h3>Mapa</h3>
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3657.3907388200637!2d-46.507282884498366!3d-23.554406067221727!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94ce6744ec87287d%3A0xd8d85e7c7d427c32!2sRua%20Palmeira%20Batu%C3%A1%2C%20199%20-%20Jardim%20Eliane%2C%20S%C3%A3o%20Paulo%20-%20SP%2C%2003575-110!5e0!3m2!1spt-BR!2sbr!4v1629122825030!5m2!1spt-BR!2sbr" width="400" height="180" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
</section>
<section class="d-flex rights justify-content-center">
    <p class="d-flex align-items-center">Sales Equipamentos e Produtos de Higiene Profissional LTDA © 2021 Todos os direitos reservados <a href="#">www.sales.com.br</a></p>
</section>