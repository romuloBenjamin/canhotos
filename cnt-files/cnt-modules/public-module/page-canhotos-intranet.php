<div class="d-flex container-page flex-column">
    <div class="d-flex container-title">
        <h1>Escaneamento de Canhotos</h1>
    </div>
    <hr>
    <!-- CONTAINER SCANNER & CERTIFICADOS -->
    <div class="d-flex justify-content-between container-scanner">
        <div class="d-flex justify-content-center align-items-center w-25">
            <!-- THIS WILL LIST ALL CRYPTS THAT WILL EXPIRE IN 30D -->
            <div id="alert-certificados" class="d-none alert-certificados">
                <h2>Certificados Disponíveis</h2>
                <ul class="list-group">
                    <li id="cloneNode" class="list-group-item"></li>
                </ul>
            </div>
        </div>
        <div id="escaneamento" class="d-flex escaneamento">
            <div class="d-flex flex-column canhotos-content">
                <span>Ações disponíveis no Scanner</span>
                <hr>
                <div class="d-flex flex-columns container-scanner-botoes">
                    <div class="d-flex align-self-start" id="buttonContainer"></div>
                    <div class="d-flex flex-column justify-content-center align-items-center extra-container"></div>
                    <!-- MESSAGE BOX -> WHERE MESSAGE WILL DISPLAY -->
                    <div class="d-none flex-column justify-content-center message-box-container">
                        <div class="d-flex justify-content-center align-items-center">
                            <div id="spinner" class="d-none spinner-border" role="status"></div>
                            <div class="message-box" id="messageBox"></div>
                        </div>
                        <div class="extra-message-box" id="extraMessageBox"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="<?= DIR_PATH; ?>cnt-modules/scanner-module/js/check-certificados-js.js"></script>
<script src="<?= DIR_PATH; ?>cnt-modules/scanner-module/js/escaneamento-js.js"></script>