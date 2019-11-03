window.addEventListener('DOMContentLoaded', function () {

    let url = {
        url: 'http://web.se-ecatalog.ru/new-api/JSON/',
        method: '',
        accessCode: '4Hw-epOswVRBPDsr3cMIViposmD1A4dA',
        commercialRef: ''
    };
    const inputArticle = document.querySelector('#articul'),
        startButton = document.querySelector('#start'),
        classInp = document.querySelector('.inp'),
        resultDiv = document.querySelector('.result');

    function postData(data) {
        return new Promise((res, rej) => {

            let request = new XMLHttpRequest();

            request.open('POST', 'ind.php');
            request.setRequestHeader('Content-Type', "application/x-www-form-urlencoded");
            let json = 'url=' + data.url +
                data.method +
                '?accessCode=' + data.accessCode +
                '&commercialRef=' + data.commercialRef;
            request.onreadystatechange = function() {
                if (request.readyState < 4) {} else if (request.readyState === 4 && request.status == 200) {
                    res(request);
                } else {
                    rej(request);
                }
            }
            // console.log(json);
            request.send(json);
        });
    }
    function createBlockArticul(item){
        const block = document.querySelectorAll('.articulmain');
        if (block.length != 0){
            block.forEach((i)=>{
                resultDiv.removeChild(i);
            });
        }
        let articul = document.createElement('div'),
            stockHTML = '';
        articul.classList.add('articulmain');
        if(item.hasOwnProperty('stocks')){

            stockHTML =`<table class="table table-striped">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Фирма</th>
                <th scope="col">Количество</th>
                <th scope="col">Дата Обновления</th>
              </tr>
            </thead>
            <tbody>`;
            num = 1;
            item.stocks.forEach(i=>{
                stockHTML +=`<tr>
                <th scope="row">${num++}</th>
                <td>${i.warehouse}</td>
                <td>${i.count}шт.</td> 
                <td>${i['last_update']}</td> 
                 </tr> ` ;
            });
            stockHTML +=`</tbody>
            </table>`;
        } else {
            stockHTML = '<span>На складе отсутствует!</span>';
        };

        articul.innerHTML=`
            <h3>${item.commercialRef}</h3>
            ${stockHTML}
            `;
            resultDiv.appendChild(articul);    
    }
    function completeResiveData(req){
        let result = JSON.parse(req.response);
        if (result.result === 'success') {
                if (result.data != null){
                result.data.forEach(item =>{
                    createBlockArticul(item);
                });
            } else {
                let dd = document.querySelector('.falseart');
                if (dd === null){
                    let falseArt = document.createElement('div');
                    falseArt.classList.add('falseart')
                    falseArt.innerText='Не правильный артикул!';
                    classInp.appendChild(falseArt, startButton)
                } else {
                    dd.style.display = 'block';
                }
            }
            
        };
    }
    function getDataFromSE(method, ref) {
        url.method = method;
        url.commercialRef = ref;

        postData(url)
            .then((req)=> completeResiveData(req))
            .catch((req) => console.log('фигня', req.readyState, req.status));
    }
    startButton.addEventListener('click', ()=>getDataFromSE('getstock', inputArticle.value));
    inputArticle.addEventListener('keydown', (e)=>{
        if(e.keyCode === 13){
            getDataFromSE('getstock', inputArticle.value)
        }
    });
    inputArticle.addEventListener('input',()=> {
        let dd = document.querySelector('.falseart');
        if (dd != null){
            dd.style.display = 'none';
        }
    });
    // url.method= 'getstock';

});