
function updateCurrentTime() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
                    
    const currentTimeString = `${year}/${month}/${day}  ${hours}:${minutes}:${seconds}`;
                    
    document.getElementById('current-time').textContent = currentTimeString;
        
    // 1秒ごとに更新
    setTimeout(updateCurrentTime, 1000);
                }
        
    // 最初の呼び出し
    updateCurrentTime();