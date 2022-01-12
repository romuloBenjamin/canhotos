<div class="container-page d-flex">
    <div class="d-flex justify-content-center align-items-center w-25">
        <!-- THIS WILL LIST ALL CRYPTS THAT WILL EXPIRE IN 30D -->
        <div id="alert-certificados" class="d-none alert-certificados">
            <ul class="list-group">
                <li id="cloneNode" class="list-group-item"></li>
            </ul>
        </div>
    </div>
    <div class="escaneamento">
        <div>
            <h2 class="text-center">Escaneamento de Canhotos</h2>
        </div>
        <div class="d-flex flex-column canhotos-content">
            <div id="buttonContainer"></div>
            <div class="extra-container">
                <button onclick="retry()" class="btn btn-warning">Identificar Novamente</button>
                <div class="d-none justify-content-center align-items-center message-box">
                    <div id="spinner" class="d-none spinner-border" role="status"></div>
                    <span class="result" id="result"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="<?= DIR_PATH; ?>cnt-modules/scanner-module/js/check-certificados-js.js"></script>
<script src="<?= DIR_PATH; ?>cnt-modules/scanner-module/js/escaneamento-js.js"></script>