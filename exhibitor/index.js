// ログインセッションの確認
setInterval(function() {
    fetch('../pwa/check_session.php')
        .then(response => response.json())
        .then(data => {
            if (data.loggedin !== true) {
                window.location.href = "../signIn/memberSignin.php";
            }
        });
}, 10000);


window.addEventListener('touchmove', preventScroll, { passive: false });
window.addEventListener('wheel', preventScroll, { passive: false });