window.addEventListener('load', function() {
    let adsTimer = 4000;
    adsLoop();
    adsLoop2();
    setInterval(function() {
        adsLoop();
        adsLoop2();
    }, adsTimer);
});

let addStatus = 1;
function adsLoop() {
    if(addStatus === 1){
        document.getElementById('adsimg2').style.opacity = '0';

        setTimeout(function () {
            document.getElementById('adsimg1').style.right = '0px';
            document.getElementById('adsimg1').style.zIndex = '1000';
            document.getElementById('adsimg2').style.right = '-1200px';
            document.getElementById('adsimg2').style.zIndex = '1500';
            document.getElementById('adsimg3').style.right = '1200px';
            document.getElementById('adsimg3').style.zIndex = '500';
        },500);
        setTimeout(function() {
            document.getElementById('adsimg2').style.opacity = '1';
        },1000);
        addStatus = 2;
    }

    else if(addStatus === 2){
        document.getElementById('adsimg3').style.opacity = '0';

        setTimeout(function () {
            document.getElementById('adsimg2').style.right = '0px';
            document.getElementById('adsimg2').style.zIndex = '1000';
            document.getElementById('adsimg3').style.right = '-1200px';
            document.getElementById('adsimg3').style.zIndex = '1500';
            document.getElementById('adsimg1').style.right = '1200px';
            document.getElementById('adsimg1').style.zIndex = '500';
        },500);
        setTimeout(function() {
            document.getElementById('adsimg3').style.opacity = '1';
        },1000);
        addStatus = 3;
    }

    else if(addStatus === 3){
        document.getElementById('adsimg1').style.opacity = '0';

        setTimeout(function () {
            document.getElementById('adsimg3').style.right = '0px';
            document.getElementById('adsimg3').style.zIndex = '1000';
            document.getElementById('adsimg1').style.right = '-1200px';
            document.getElementById('adsimg1').style.zIndex = '1500';
            document.getElementById('adsimg2').style.right = '1200px';
            document.getElementById('adsimg2').style.zIndex = '500';
        },500);
        setTimeout(function() {
            document.getElementById('adsimg1').style.opacity = '1';
        },1000);
        addStatus = 1;
    }
}

let addStatus2 = 1;
function adsLoop2() {
    if(addStatus2 === 1){
        document.getElementById('adsimg5').style.opacity = '0';

        setTimeout(function () {
            document.getElementById('adsimg4').style.left = '0px';
            document.getElementById('adsimg4').style.zIndex = '1100';
            document.getElementById('adsimg5').style.left = '-1000px';
            document.getElementById('adsimg5').style.zIndex = '1600';
            document.getElementById('adsimg6').style.left = '1000px';
            document.getElementById('adsimg6').style.zIndex = '600';
        },500);
        setTimeout(function() {
            document.getElementById('adsimg5').style.opacity = '1';
        },1000);
        addStatus2 = 2;
    }

    else if(addStatus2 === 2){
        document.getElementById('adsimg6').style.opacity = '0';

        setTimeout(function () {
            document.getElementById('adsimg5').style.left = '0px';
            document.getElementById('adsimg5').style.zIndex = '1100';
            document.getElementById('adsimg6').style.left = '-1000px';
            document.getElementById('adsimg6').style.zIndex = '1600';
            document.getElementById('adsimg4').style.left = '1000px';
            document.getElementById('adsimg4').style.zIndex = '600';
        },500);
        setTimeout(function() {
            document.getElementById('adsimg6').style.opacity = '1';
        },1000);
        addStatus2 = 3;
    }

    else if(addStatus2 === 3){
        document.getElementById('adsimg4').style.opacity = '0';

        setTimeout(function () {
            document.getElementById('adsimg6').style.left = '0px';
            document.getElementById('adsimg6').style.zIndex = '1100';
            document.getElementById('adsimg4').style.left = '-1000px';
            document.getElementById('adsimg4').style.zIndex = '1600';
            document.getElementById('adsimg5').style.left = '1000px';
            document.getElementById('adsimg5').style.zIndex = '600';
        },500);
        setTimeout(function() {
            document.getElementById('adsimg4').style.opacity = '1';
        },1000);
        addStatus2 = 1;
    }
}

function scamaz() {
    alert('You have been scammed!');
}