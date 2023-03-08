// To use Html5QrcodeScanner (more info below)
import {Html5Qrcode} from "html5-qrcode"

function onScanSuccess(decodedText, decodedResult) {
    const form = document.getElementById("scanner-form");
    form.querySelector('input[name="qr_code"]').value = decodedText;
    form.submit();
}

function onScanFailure(error) {
    return;
}

// const html5QrcodeScanner = new Html5QrcodeScanner(
//     "scanner",
//     { fps: 10, qrbox: {width: 250, height: 250} },
//     false
// );
// html5QrcodeScanner.render(onScanSuccess, onScanFailure);

const html5QrCode = new Html5Qrcode("scanner", false);
html5QrCode.start(
    { facingMode: { exact: "environment" } },
    { fps: 10, qrbox: {width: 250, height: 250} },
    onScanSuccess,
    onScanFailure
)
.then(_ => {
    console.log("Started successfully");
}).catch((error) => {
    alert("Une erreur est survenue lors du démarrage de la caméra. Essayez sur un teléphone mobile");
});
