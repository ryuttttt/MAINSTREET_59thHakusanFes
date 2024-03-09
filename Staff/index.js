//ログインセッションの確認
setInterval(function() {
    fetch('./pwa/check_session.php')
        .then(response => response.json())
        .then(data => {
            if (data.loggedin !== true) {
                window.location.href = "../SignIn/login.php";
            }
         });
            }, 10000);

/*スクロールの禁止
window.addEventListener('touchmove', function (e) {
    e.preventDefault();
}, { passive: false });

window.addEventListener('wheel', function (e) {
    e.preventDefault();
}, { passive: false });
*/
//画面縦幅サイズの計算
function setHeight() {
    let vh = window.innerHeight * 0.01;
    document.documentElement.style.setProperty('--vh', `${vh}px`);
  }
  
  // 2.初期化
  setHeight();
  
  // 3.ブラウザのサイズが変更された時・画面の向きを変えた時に再計算する
  window.addEventListener('resize', setHeight);


//QRコードを表示ボタンを押すと、そのボタンを含むdivタグが非表示に、
//QRコードを含むuser-containerのクラスを持つdivタグを表示する script
document.addEventListener('DOMContentLoaded', function() {
    let displayQRButton = document.getElementById('displayQR');
    let selectDiv = document.getElementById('select');
    let iconDiv = document.getElementById('icon')
    let userContainerDiv = document.querySelector('.user-container');
    let displayMenuButton = document.getElementById('displayMenu');

    displayQRButton.addEventListener('click', function() {
        // select divを非表示にする
        selectDiv.style.display = 'none';
        iconDiv.style.display ='none';

        // user-container divを表示する
        userContainerDiv.style.display = 'block';

        // displayMenuボタンを表示する
        displayMenuButton.style.display = 'block';
    });
});

// Scannerボタンを押したとき
document.addEventListener("DOMContentLoaded", function() {
    let btn = document.getElementById("qrScanner");
    btn.addEventListener("click", function() {
        location.href = "../Scanner";
    });
});




//メニューを表示するボタン、押すとそれとuser-containerを非表示、selectのidを持つdivタグを表示
document.addEventListener('DOMContentLoaded', function() {
    // ボタンとdiv要素を取得
    let displayMenuButton = document.getElementById('displayMenu');
    let selectDiv = document.getElementById('select');
    let userContainerDiv = document.querySelector('.user-container');

    displayMenuButton.addEventListener('click', function() {
        // displayMenuボタンを非表示にする
        displayMenuButton.style.display = 'none';

        // user-container divを非表示にする
        userContainerDiv.style.display = 'none';

        // select divを表示する
        selectDiv.style.display = 'block';
    });
});

