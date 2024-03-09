//qrCode生成部分
const qrCode = new QRCodeStyling({
    width: 300,
    height: 300,
    type: "svg",
    data: uniqueId,
    image: "../images/exhibitor.png",
    qrOptions: {
        errorCorrectionLevel: 'H'
    },
    dotsOptions: {
        color: "#3f469d",
        type: "square"
    },
    cornersSquareOptions:{
        type: "square"
    },
    cornersDotOptions: {
        type: "square"
    },
    backgroundOptions: {
        color: "#fff",
    },
    imageOptions: {
        crossOrigin: "anonymous",
        margin: 0,
    }
});

qrCode.append(document.getElementById('qrCode'));