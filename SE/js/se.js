window.addEventListener('DOMContentLoaded', function () {
    let url = {'url' : 'http://web.se-ecatalog.ru/new-api/JSON/configurable?accessCode=JwaqjUxZP26ILPdKCXffRgd3PC9f-VW0&commercialRef=SDN6000347'};
    let request = new XMLHttpRequest();

    request.open('POST', 'ind.php');
    request.setRequestHeader('Content-Type', 'application/json; charset=utf-8');
    let json = JSON.stringify(url);
    console.log(json);
    request.send(json);

    request.addEventListener('readystatechange', () => {
        if (request.readyState < 4) {

        } else if (request.readyState === 4 && request.status == 200) {
            console.log(request.response);
            } else {
            console.log('фигня',request.readyState,request.status);
        }

    });
});