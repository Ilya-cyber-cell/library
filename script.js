// define the tree-item component
Vue.component("tree-item", {
    template: "#item-template",
    props: {
        item: Object
    },
    data: function() {
        return {
        isOpen: false
        };
    },
    computed: {
        isFolder: function() {
        return this.item.children && this.item.children.length;
        }
    },
    methods: {
        toggle: function() {
        if (this.isFolder) {
            if (this.isOpen){
                this._props.item.children=[{"name":"Загрузка.."}];
            } else{
                let jsonData = {"actons":"getCreators","patern":this._props.item.name};
                postData = new FormData()
                postData.append('json', JSON.stringify(jsonData))
                apiRequest(postData).then(data =>{
                    if(data.error==0) {
                        this._props.item.children = data.content;
                    }else{
                        console.log(data.content)
                    }
                }).catch(err => {
                    console.log('Fetch Error', err);
                });                
            }
            this.isOpen = !this.isOpen;
            console.log(this)
        }
        }
    }
});

var creators = new Vue({
    el: "#creators",
    data: {
          treeData:   []
    },
    methods: {
        loadAlphabet:function () {
            let jsonData = {"actons":"getFirstLetter"};
            postData = new FormData()
            postData.append('json', JSON.stringify(jsonData))
            apiRequest(postData).then(data =>{
                if(data.error==0) {
                    data.content.forEach(item => {
                        this.treeData.push({name:item,children:[{"name":"Загрузка.."}]});
                    });
                }else{
                    console.log(data.content)
                }
            }).catch(err => {
                console.log('Fetch Error', err);
            }); 
        }
    },
    created:function () {
        this.loadAlphabet();
    }
});
    
//Функция для работы с апи. Возвращает Promise 
function apiRequest(data){
    return fetch('./api.php',{
        method: 'POST',
        cache: 'no-cache',
        credentials: "same-origin",
        body: data
    })
    .then(status)
    .then(json)
}

function status(response) {  
    if (response.status >= 200 && response.status < 300) {  
        return Promise.resolve(response)  
    } else {  
        return Promise.reject(new Error(response.statusText))  
    }  
}
function json(response) {
    console.log(response)
    return response.json()  
}

