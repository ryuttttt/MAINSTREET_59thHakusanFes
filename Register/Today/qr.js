let player = document.getElementById('video');
let canvas = document.getElementById('canvas');
let width = canvas.width;
let height = canvas.height;
let intervalHandler;
let qrInput = document.getElementById('qr');
let submitButton = document.getElementById('submitButton');
let responseContainer = document.getElementById('responseContainer');
let myForm = document.getElementById('myForm');

let startButton = document.getElementById('startButton');
let closeButton = document.getElementById('closeButton');

let hasInvalidQRAlertShown = false; // アラートを一度表示したかどうかを示すフラグ

let startScan = function (callback) {
  let canvasContext = canvas.getContext('2d');
  intervalHandler = setInterval(function () {
    canvasContext.drawImage(player, 0, 0, width, height);
    let imageData = canvasContext.getImageData(0, 0, width, height);
    let scanResult = jsQR(imageData.data, imageData.width, imageData.height);

    if (scanResult) {
      let qrCodeData = scanResult.data;
      clearInterval(intervalHandler);

      if (!qrCodeData.startsWith('T-')) {
        if (!hasInvalidQRAlertShown) {
          alert('無効なIDです。T-で始まる当日登録用QRコードではありません。');
          hasInvalidQRAlertShown = true; // アラートを表示済みに設定
          qrInput.value = '';
          window.location.reload();
        }
      } else {
        callback(scanResult);
      }
    }
  });
};


let handleSuccess = function (stream) {
  player.srcObject = stream;

  startScan(function (scanResult) {
    qrInput.value = scanResult.data;
    submitButton.disabled = false;
  });
};

let startCamera = function () {
  // ボタンの無効化
  startButton.disabled = true;

  if (navigator.mediaDevices) {
    navigator.mediaDevices.getUserMedia({
      video: {
        facingMode: 'environment',
        width: 320,
        height: 320,
      },
      audio: false,
    })
      .then(function (stream) {
        handleSuccess(stream);
        // 閉じるボタンの有効化
        closeButton.disabled = false;
      })
      .catch(function (err) {
        console.log(JSON.stringify(err));
        // ボタンの有効化
        startButton.disabled = false;
      });
  } else {
    alert('ビデオカメラを使用できません');
    // ボタンの有効化
    startButton.disabled = false;
  }
};

let stopCamera = function () {
  // ボタンの無効化
  closeButton.disabled = true;

  if (intervalHandler) {
    clearInterval(intervalHandler);
  }

  if (player.srcObject) {
    let tracks = player.srcObject.getTracks();
    tracks.forEach(function (track) {
      track.stop();
    });
    player.srcObject = null;
  }

  // ボタンの有効化
  startButton.disabled = false;
  submitButton.disabled = true;

  // フォームの値をクリア
  qrInput.value = '';

  // ページのリロード
  window.location.reload();
};

let submitData = function () {
  // inquiry.phpに値を渡す処理を削除

  // フォームをリセット
  myForm.reset();
};

document.getElementById('startButton').addEventListener('click', startCamera);
document.getElementById('closeButton').addEventListener('click', stopCamera);
document.getElementById('submitButton').addEventListener('click', submitData);