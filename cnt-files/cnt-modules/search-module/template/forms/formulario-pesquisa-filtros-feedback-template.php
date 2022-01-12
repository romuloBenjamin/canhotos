<form action="javascript: void(0);" method="get" class="d-flex search-filters-form" id="filtrar-listagem">

    <div class="d-flex flex-column filter-container">
        <div class="text-center filter-label"><small>Motivo</small></div>
        <select name="forMotivos" id="forMotivos" class="form-control">
            <option value="*">Selecione o motivo</option>
        </select>
    </div>

    <div class="d-flex flex-column filter-container">
        <div class="text-center filter-label"><small>Filial</small></div>
        <select name="forEmpresa" id="forEmpresa" class="form-control">
            <option value="*">Selecione a filial</option>
        </select>
    </div>

    <div class="d-flex flex-column filter-container">
        <div class="text-center filter-label"><small>NFe | Vendedor | Cliente</small></div>
        <input type="text" name="search" id="search" placeholder="NF|Vendedor|Cliente" class="form-control">
    </div>

    <div class="d-flex flex-column search-button-container">
        <div class="filter-label">&nbsp;</div>
        <button id="forSearch" class="btn btn-secondary"><i class="fas fa-search"></i></button>
    </div>
</form>

<script src="<?= DIR_PATH; ?>cnt-modules/search-module/js/create-select-list-js.js" defer></script>