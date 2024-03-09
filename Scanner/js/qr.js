// SQRオブジェクトが存在しない場合は初期化
window.SQR = window.SQR || {};

// QRコードリーダーモジュール
SQR.reader = (() => {
    // サポートされていない場合の画面表示
    const showUnsupportedScreen = () => {
        document.querySelector('#js-unsupported').classList.add('is-show');
    };

    if (!navigator.mediaDevices) {
        showUnsupportedScreen();
        return;
    }

    // カメラ関連の要素
    const video = document.querySelector('#js-video');
    const canvas = document.querySelector('#js-canvas');
    const ctx = canvas.getContext('2d');

    // スキャン状態と最後のQRコード情報の初期化
    let scanning = false;
    let lastQRCodeData = null;
    let lastQRCodeTimestamp = 0;

    // QRコードの読み取り
    const checkQRUseLibrary = () => {
        if (!scanning) return;

        // カメラから画像をキャプチャ
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, canvas.width, canvas.height);

        if (code) {
            const qrCodeData = code.data;

            // 2秒以内に同じQRコードを再スキャンしないようにする
            if (qrCodeData === lastQRCodeData && (Date.now() - lastQRCodeTimestamp) < 2000) {
                requestAnimationFrame(checkQRUseLibrary);
                return;
            }

            lastQRCodeData = qrCodeData;
            lastQRCodeTimestamp = Date.now();

            const selectedLocation = document.querySelector('#location').value;

            if (!qrCodeData.startsWith('S-') && !qrCodeData.startsWith('G-') && !qrCodeData.startsWith('E-')) {
                alert('無効なIDです。');
                setTimeout(() => {
                    initCamera();
                    scanning = true;
                    requestAnimationFrame(checkQRUseLibrary);
                }, 1000);
                return;
            }

            const timestamp = new Date().toLocaleString();
            const entryData = {
                qrCodeData,
                location: selectedLocation,
                created_at: timestamp,
            };

            let storedEntries = JSON.parse(localStorage.getItem('qrEntries')) || [];
            storedEntries.push(entryData);
            localStorage.setItem('qrEntries', JSON.stringify(storedEntries));

            // ローカルストレージにデータがセットされたときに白枠を赤く点滅させる
            const reticleBox = document.querySelector('.reticle-box');
            reticleBox.classList.add('flash-red');

            setTimeout(() => {
                reticleBox.classList.remove('flash-red');
            }, 1000);

            document.querySelector('.return-button a').addEventListener('click', function (e) {
                e.preventDefault();

                const storedEntries = JSON.parse(localStorage.getItem('qrEntries'));

                if (storedEntries && storedEntries.length > 0) {
                    const csvData = 'unique_id,location,created_at\n' +
                        storedEntries.map(entry => `${entry.qrCodeData},${entry.location},${entry.created_at}`).join('\n');

                    // CSVデータを生成中のアラート表示
                    alert('CSVデータを生成しています...');

                    // 生成したCSVデータをサーバーに送信
                    fetch('./php/postman.php', {
                        method: 'POST',
                        body: csvData,
                        headers: {
                            'Content-Type': 'text/csv',
                        },
                    })
                    .then(response => {
                        if (response.ok) {
                            alert('CSVファイルがアップロードされました。');
                            console.log('CSVファイルがアップロードされました。');
                        } else {
                            alert('アップロードエラー: ' + response.status);
                            console.error('アップロードエラー:', response.status);
                        }
                    })
                    .catch(error => {
                        alert('エラー: ' + error);
                        console.error('エラー:', error);
                    });

                    // ローカルストレージをクリア
                    localStorage.removeItem('qrEntries');


                } else {
                    window.location.href = '../Staff/index.php';
                }
            });
            
            document.querySelector('#qr-value').value = qrCodeData;
            document.querySelector('#location-value').value = selectedLocation;
            
            requestAnimationFrame(checkQRUseLibrary);

        } else {
            requestAnimationFrame(checkQRUseLibrary);
        }
    };

    // カメラの初期化
    const initCamera = () => {
        navigator.mediaDevices
            .getUserMedia({
                audio: false,
                video: {
                    facingMode: {
                        exact: 'environment',
                    },
                },
            })
            .then((stream) => {
                video.srcObject = stream;
                video.onloadedmetadata = () => {
                    video.play();
                    scanning = true;
                    requestAnimationFrame(checkQRUseLibrary);
                };
            })
            .catch(() => {
                showUnsupportedScreen();
            });
    };

    // QRコード読み取りの再開
    const resumeScanning = () => {
        if (!scanning) {
            scanning = true;
            requestAnimationFrame(checkQRUseLibrary);
        }
    };

    // 1秒後にQRコード読み取りを再開
    setTimeout(resumeScanning, 1000);

    return {
        initCamera,
    };
})();

// モーダル表示モジュール
SQR.modal = (() => {
    const result = document.querySelector('#js-result');
    const modal = document.querySelector('#js-modal');

    const open = (url) => {
        result.value = url;
        modal.classList.add('is-show');

        setTimeout(() => {
            modal.classList.remove('is-show');
        }, 500);
    };

    return {
        open,
    };
})();

if (SQR.reader) SQR.reader.initCamera();

document.addEventListener("DOMContentLoaded", function () {
    const locationSelect = document.querySelector("#location");
    const selectedLocation = sessionStorage.getItem("selected_location");

    if (selectedLocation) {
        locationSelect.value = selectedLocation;
    }

    locationSelect.addEventListener("change", function () {
        const selectedValue = locationSelect.value;
        sessionStorage.setItem("selected_location", selectedValue);
    });
});
