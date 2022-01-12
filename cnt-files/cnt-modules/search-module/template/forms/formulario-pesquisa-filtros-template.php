<form action="javascript: void(0);" method="get" class="d-flex search-filters-form" id="filtrar-listagem">
    <!-- Filtros BASICOS -->
    <div class="d-flex flex-column filter-container">
        <div class="text-center filter-label"><small>Motorista</small></div>
        <select name="forMotorista" id="forMotorista" class="form-control">
            <option value="*">Selecione o Motorista</option>
        </select>
    </div>

    <div class="d-flex flex-column filter-container">
        <div class="text-center filter-label"><small>Equipe</small></div>
        <select name="forEquipe" id="forEquipe" class="form-control">
            <option value="*">Selecione a equipe</option>
        </select>
    </div>

    <div class="d-flex flex-column filter-container">
        <div class="text-center filter-label"><small>Motivo</small></div>
        <select name="forMotivos" id="forMotivos" class="form-control">
            <option value="*">Selecione o motivo</option>
        </select>
    </div>

    <div class="d-flex flex-column filter-container">
        <div class="text-center filter-label"><small>NF|N° Romaneio|N° Motivo</small></div>
        <input type="text" name="search" id="search" placeholder="NF|N.Romaneio|N.Motivo" class="form-control">
    </div>

    <div class="d-flex flex-column search-button-container">
        <div class="filter-label">&nbsp;</div>
        <button id="forSearch" class="btn btn-secondary"><i class="fas fa-search"></i></button>
    </div>
</form>
<script src="<?= DIR_PATH; ?>cnt-modules/search-module/js/create-select-list-js.js" defer></script>