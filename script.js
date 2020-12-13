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
                let jsonData = {"actons":"getCreators","patern":this._props.item.name,"module":"Search"};
                apiRequest(jsonData).then(data =>{
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
        } else{
            Books.load(this._props.item.id);
        }
        }
    }
});
Vue.component("modal", {
    template: "#modal-template"
});
Vue.component('treeselect', VueTreeselect.Treeselect)

var topPanel = new Vue({
    el: "#topPanel",
    methods:{
        openLoginFrom:function() {
            loginForm.openLoginFrom();
        }, 
        openUsersForm:function() {
            usersForm.openFrom();
        },
    }
    
});

var loginForm = new Vue({
    el: "#loginForm",
    data: {
        showLoginWindow: false,
        login: '',
        password:''
    },methods:{
        openLoginFrom:function() {
            this.showLoginWindow = true;
        },
        loginBtn: function() {
            console.log('login');
            this.showLoginWindow = false;
            let jsonData = {"actons":"login","module":"User","login":this.login,"password":this.password};
            this.password='';
            apiRequest(jsonData).then(data =>{
                if(data.error==0) {
                    console.log(data.content)
                }else{
                    console.log(data.content)
                }
            }).catch(err => {
                console.log('Fetch Error', err);
            }); 
        },
        closeBtn: function() {
            this.showLoginWindow = false;
        }        
    }
});

var bookForm = new Vue({
    el: "#bookForm",
    data: {
        showWindow: false,
        book:{},
        Allgenres:[],
        AllLanguage:[],
        AllTypes:[],
        AviableBooks:[]
    },methods:{
        openBookFrom:function(bookId) {
            let jsonData = {"actons":"getBook","module":"Book","bookId":bookId};
            apiRequest(jsonData).then(data =>{
            if(data.error==0) {
                    this.book=data.content.book;
                    this.Allgenres = data.content.Allgenres;
                    this.AllLanguage = data.content.AllLanguage;
                    this.AllTypes = data.content.AllTypes;
                    this.AviableBooks = data.content.AviableBooks;
                    this.showWindow = true;
                }else{
                    console.log(data.content)
                }
            }).catch(err => {
                console.log('Fetch Error', err);
            }); 
        },
        saveBtn:function() {
            console.log(this.book.genres);
            let jsonData = {"actons":"save","module":"Book","book":this.book};
            apiRequest(jsonData).then(data =>{
            if(data.error==0) {
                    this.showWindow = false;
                }else{
                    console.log(data.content)
                }
            }).catch(err => {
                console.log('Fetch Error', err);
            });
        },        
        closeBtn: function() {
            this.showWindow = false;
        }        
    }
});

var usersForm = new Vue({
    el: "#usersForm",
    data: {
        showWindow: false,
        users: []
    },
    methods: {
        openFrom:function(bookId) {
            let jsonData = {"actons":"getUsers","module":"Search"};
            apiRequest(jsonData).then(data =>{
            if(data.error==0) {
                    this.users=data.content;
                    this.showWindow = true;
                }else{
                    console.log(data.content)
                }
            }).catch(err => {
                console.log('Fetch Error', err);
            }); 
        },
        openUser:function(userId) {
            userForm.openForm(userId);       
        }
    }
    
})
var userForm = new Vue({
    el: "#userForm",
    data: {
        showWindow: false,
        user:{},
        password:"",
        passwordInvalide:false,
        roles:[]
    },methods:{
        openForm:function(userId) {
            let jsonData = {"actons":"getUser","module":"User","userId":userId};
            apiRequest(jsonData).then(data =>{
            if(data.error==0) {
                    this.user=data.content.user;
                    this.roles=data.content.roles;
                    this.showWindow = true;
                }else{
                    console.log(data.content)
                }
            }).catch(err => {
                console.log('Fetch Error', err);
            }); 
        },
        saveBtn:function() {
            if (this.password == this.user.password){    
                this.passwordInvalide=false;
                let jsonData = {"actons":"save","module":"User","user":this.user};
                apiRequest(jsonData).then(data =>{
                if(data.error==0) {
                        this.showWindow = false;
                    }else{
                        console.log(data.content)
                    }
                }).catch(err => {
                    console.log('Fetch Error', err);
                });
            }else{
                this.passwordInvalide=true;
            }
        },        
        closeBtn: function() {
            this.showWindow = false;
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
            let jsonData = {"actons":"getFirstLetter","module":"Search"};
            apiRequest(jsonData).then(data =>{
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
var Books = new Vue({
    el: '#books',
    data:{
        books:[]
    },
    methods:{
        load:function (creatorId) {
            console.log(creatorId);
            let jsonData = {"actons":"getBooks","creatorId":creatorId,"module":"Search"};
            apiRequest(jsonData).then(data =>{
                if(data.error==0) {
                    this.books=data.content;
                }else{
                    console.log(data.content)
                }
            }).catch(err => {
                console.log('Fetch Error', err);
            });
        },
        openBookFrom:function (bookId) {
            bookForm.openBookFrom(bookId);
        }
    }
    
})


//Функция для работы с апи. Возвращает Promise 
function apiRequest(jsonData){
    postData = new FormData()
    postData.append('json', JSON.stringify(jsonData))
    return fetch('./api.php',{
        method: 'POST',
        cache: 'no-cache',
        credentials: "same-origin",
        body: postData
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
    console.log("response")
    console.log(response)
    return response.json()  
}

