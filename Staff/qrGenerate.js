//qrCode生成部分
const qrCode = new QRCodeStyling({
    width: 250,
    height: 250,
    type: "svg",
    data: uniqueId,
    image: "../images/59staff.png",
    qrOptions: {
        errorCorrectionLevel: 'H'
    },
    dotsOptions: {
        color: "#4267b2",
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