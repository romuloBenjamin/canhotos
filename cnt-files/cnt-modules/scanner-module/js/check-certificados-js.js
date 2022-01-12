async function getExpiredDigitalSigns() {
    var certificados = await axios.get("./cnt-files/cnt-modules/scanner-module/core/listar-certificados-vencidos-core.php");    
    var placer_certificados = document.querySelector("div#alert-certificados");
    if(certificados.data.length > 1){
        for (let index = 0; index < certificados.data.length; index++) {
            const certificados_instalados = certificados.data[index];
            const cloner = placer_certificados.querySelector("ul > li#cloneNode").cloneNode(true);
            cloner.id = "certificado-"+certificados_instalados.cnpj;
            cloner.innerHTML = "<b>Certificado:</b> "+certificados_instalados.cnpj;
            cloner.innerHTML += "<br> <b>Empresa:</b> "+certificados_instalados.empresa;
            cloner.innerHTML += "<br> <b>Validade:</b> "+certificados_instalados.expire;
            cloner.innerHTML += "<br> <b>Situação:</b> ";
            if(certificados_instalados.timer === "Certificado Expirado!") {
                cloner.innerHTML += "<span>" + certificados_instalados.timer + "</span>";
                sendExpiredCertMail(certificados_instalados);
            } else {
                cloner.innerHTML += certificados_instalados.timer;
            }
            placer_certificados.querySelector("ul").appendChild(cloner);
        }
        if(placer_certificados.classList.contains("d-none")) placer_certificados.classList.remove("d-none");
        placer_certificados.querySelector("li#cloneNode").remove();
    }    
}
getExpiredDigitalSigns();

const sendExpiredCertMail = async (certificado) => {
    console.log("Atualizar certificado");
    const response = await axios.post("./cnt-files/cnt-modules/scanner-module/core/send-expired-cert-mail-core.php", {
        certificado: certificado
    });
    console.log(response);
}