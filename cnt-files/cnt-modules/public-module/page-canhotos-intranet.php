<div class="d-flex container-page flex-column">
    <div class="d-flex container-title">
        <h1>Escaneamento de Canhotos</h1>
    </div>
    <hr>
    <!-- CONTAINER SCANNER & CERTIFICADOS -->
    <div class="d-flex align-items-start justify-content-center container-scanner">
        <!-- THIS WILL LIST ALL CRYPTS THAT WILL EXPIRE IN 30D -->
        <div id="alert-certificados" class="d-flex flex-column alert-certificados justify-content-center align-items-center">
            <h2>Certificados</h2>
            <ul class="list-group">
                <li id="cloneNode" class="list-group-item"></li>
            </ul>
        </div>
        <div class="d-flex flex-column canhotos-content">
            <div class="d-flex align-items-center available-actions">
                <span>Ações disponíveis</span>
                <div id="spinner" class="d-none spinner-border" role="status"></div>
            </div>

            <hr>
            <div class="d-flex main-container">
                <div class="d-flex align-items-start align-self-start" id="buttonContainer"></div>
                <div class="d-none flex-column justify-content-center align-self-start extra-container">
                    <div class="message-box" id="messageBox"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="dialog" title="Confirmação" class="d-none">
    <p>Deseja escanear mais canhotos ou iniciar o processo de identificação?</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="<?= DIR_PATH; ?>cnt-modules/scanner-module/js/check-certificados-js.js"></script>
<script src="<?= DIR_PATH; ?>cnt-modules/scanner-module/js/escaneamento-js.js"></script>