// To use Html5QrcodeScanner (more info below)
import {Html5QrcodeScanner} from "html5-qrcode"

function onScanSuccess(decodedText, decodedResult) {
    const form = document.getElementById("scanner-form");
    form.querySelector('input[name="qr_code"]').value = decodedText;
    form.submit();
}

function onScanFailure(error) {
    return;
}

const html5QrcodeScanner = new Html5QrcodeScanner(
    "scanner",
    { fps: 10, qrbox: {width: 250, height: 250} },
    false
);
html5QrcodeScanner.render(onScanSuccess, onScanFailure);
